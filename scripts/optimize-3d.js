import { Document, NodeIO } from '@gltf-transform/core';
import { draco } from '@gltf-transform/functions';
import { readFile, writeFile, mkdir } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import { resolve, dirname, extname, basename } from 'node:path';

const [, , inputPath, outputPath, settingsJson] = process.argv;

if (!inputPath || !outputPath) {
  console.error('Usage: node optimize-3d.js <input> <output> [settings]');
  process.exit(1);
}

const settings = settingsJson ? JSON.parse(settingsJson) : {};
const quantizationBits = settings.quantization_bits ?? 14;
const compressionLevel = settings.compression_level ?? 7;

async function optimize() {
  const input = resolve(inputPath);
  const output = resolve(outputPath);

  if (!existsSync(input)) {
    console.error(`Input file not found: ${input}`);
    process.exit(1);
  }

  const outputDir = dirname(output);
  if (!existsSync(outputDir)) {
    await mkdir(outputDir, { recursive: true });
  }

  const ext = extname(input).toLowerCase();
  if (!['.glb', '.gltf'].includes(ext)) {
    console.log(`Unsupported format: ${ext}. Only GLB/GLTF files are supported.`);
    console.log('Converting unsupported formats requires manual conversion first.');
    process.exit(1);
  }

  const io = new NodeIO();
  const document = await io.read(input);

  await document.transform(
    draco({
      method: 'EDGEBREAKER',
      encodeSpeed: compressionLevel,
      decodeSpeed: 10 - compressionLevel,
      quantizationPosition: quantizationBits,
      quantizationNormal: quantizationBits,
      quantizationColor: quantizationBits,
      quantizationTexcoord: quantizationBits,
      quantizationGeneric: quantizationBits,
    })
  );

  await io.write(output, document);

  const originalSize = existsSync(input) ? (await readFile(input)).length : 0;
  const compressedSize = (await readFile(output)).length;
  const ratio = originalSize > 0 ? ((1 - compressedSize / originalSize) * 100).toFixed(2) : 0;

  console.log(JSON.stringify({
    success: true,
    original_size: originalSize,
    compressed_size: compressedSize,
    ratio: parseFloat(ratio),
    quantization_bits: quantizationBits,
    compression_level: compressionLevel,
    output_path: output,
  }));
}

optimize().catch((err) => {
  console.error(JSON.stringify({ success: false, error: err.message }));
  process.exit(1);
});
