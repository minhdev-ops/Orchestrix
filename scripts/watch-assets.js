import { watch } from 'chokidar';
import { execSync } from 'node:child_process';
import { existsSync, mkdirSync } from 'node:fs';
import { resolve, join, relative, extname, dirname } from 'node:path';

const SUPPORTED = ['.glb', '.gltf'];
const SCRIPTS_DIR = new URL('.', import.meta.url).pathname;

const watchDir = process.argv[2] || resolve('storage/app/public/3d-assets/original');
const outputBase = process.argv[3] || resolve('storage/app/public/3d-assets/compressed');

if (!existsSync(watchDir)) {
  console.error(`Watch directory not found: ${watchDir}`);
  process.exit(1);
}

console.log(`Watching for new 3D assets in: ${watchDir}`);

const watcher = watch(`${watchDir}/**/*`, {
  ignored: /(^|[\/\\])\../,
  persistent: true,
  awaitWriteFinish: {
    stabilityThreshold: 2000,
    pollInterval: 100,
  },
});

watcher
  .on('add', (filePath) => {
    if (!SUPPORTED.includes(extname(filePath).toLowerCase())) return;

    const relPath = relative(watchDir, filePath);
    const outputPath = join(outputBase, relPath);
    const outDir = dirname(outputPath);

    if (!existsSync(outDir)) {
      mkdirSync(outDir, { recursive: true });
    }

    const settings = JSON.stringify({ quantization_bits: 14, compression_level: 7 });
    const cmd = `node ${join(SCRIPTS_DIR, 'optimize-3d.js')} ${filePath} ${outputPath} '${settings}'`;

    console.log(`[NEW] ${relPath} - starting compression...`);

    try {
      const stdout = execSync(cmd, { encoding: 'utf-8', timeout: 120000 });
      const result = JSON.parse(stdout.trim().split('\n').pop());
      if (result.success) {
        console.log(`[DONE] ${relPath} - ${(result.original_size / 1024).toFixed(1)}KB -> ${(result.compressed_size / 1024).toFixed(1)}KB (${result.ratio}% saved)`);
      } else {
        console.error(`[FAIL] ${relPath}: ${result.error}`);
      }
    } catch (err) {
      console.error(`[FAIL] ${relPath}: ${err.message}`);
    }
  })
  .on('error', (err) => console.error(`Watcher error: ${err}`));
