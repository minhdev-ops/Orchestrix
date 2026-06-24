import { execSync } from 'node:child_process';
import { readdirSync, existsSync, mkdirSync } from 'node:fs';
import { resolve, join, relative, extname } from 'node:path';

const SUPPORTED = ['.glb', '.gltf'];
const SCRIPTS_DIR = new URL('.', import.meta.url).pathname;

const inputDir = process.argv[2] || resolve('storage/app/public/3d-assets/original');
const outputBase = process.argv[3] || resolve('storage/app/public/3d-assets/compressed');

if (!existsSync(inputDir)) {
  console.error(`Input directory not found: ${inputDir}`);
  process.exit(1);
}

function walkDir(dir) {
  const entries = readdirSync(dir, { withFileTypes: true });
  const files = [];
  for (const entry of entries) {
    const full = join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...walkDir(full));
    } else if (SUPPORTED.includes(extname(entry.name).toLowerCase())) {
      files.push(full);
    }
  }
  return files;
}

const files = walkDir(inputDir);
console.log(`Found ${files.length} 3D file(s) to optimize.`);

let success = 0;
let failed = 0;

for (const file of files) {
  const relPath = relative(inputDir, file);
  const outputPath = join(outputBase, relPath);

  const outDir = outputPath.substring(0, outputPath.lastIndexOf('/'));
  if (!existsSync(outDir)) {
    mkdirSync(outDir, { recursive: true });
  }

  const settings = JSON.stringify({
    quantization_bits: 14,
    compression_level: 7,
  });

  const cmd = `node ${join(SCRIPTS_DIR, 'optimize-3d.js')} ${file} ${outputPath} '${settings}'`;

  try {
    const stdout = execSync(cmd, { encoding: 'utf-8', timeout: 120000 });
    const result = JSON.parse(stdout.trim().split('\n').pop());
    if (result.success) {
      console.log(`[OK] ${relPath} - compressed ${(result.original_size / 1024).toFixed(1)}KB -> ${(result.compressed_size / 1024).toFixed(1)}KB (${result.ratio}%)`);
      success++;
    } else {
      console.error(`[FAIL] ${relPath}: ${result.error}`);
      failed++;
    }
  } catch (err) {
    console.error(`[FAIL] ${relPath}: ${err.message}`);
    failed++;
  }
}

console.log(`\nDone. ${success} succeeded, ${failed} failed.`);
process.exit(failed > 0 ? 1 : 0);
