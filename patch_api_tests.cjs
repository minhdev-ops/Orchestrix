const fs = require('fs');
const file = 'tests/playwright/generated/scenario_8_api_rest_module_.spec.ts';
let content = fs.readFileSync(file, 'utf-8');

const lines = content.split('\n');
let outLines = [];
let currentApiMethod = 'GET';
let currentApiUrl = '';

for (let i = 0; i < lines.length; i++) {
    let line = lines[i];

    // Detect the API endpoint from the test name
    const match = line.match(/test\('TC-\d+: (GET|POST|PUT|DELETE) ([^']+)'/);
    if (match) {
        currentApiMethod = match[1];
        currentApiUrl = match[2].replace(/\{.*?\}/g, '1'); // Replace path params with '1'
        if (!currentApiUrl.startsWith('/agriverse')) {
           currentApiUrl = '/agriverse' + currentApiUrl;
        }
        
        // Ensure the test uses { request } instead of { page }
        line = line.replace('({ page })', '({ request })');
        line = line.replace('({ page, request })', '({ request })');
        outLines.push(line);
        outLines.push('      let response;');
        continue;
    }

    // Replace interaction step with real API call
    if (line.includes('// API interaction simulation') || line.includes('// TODO: Implement interaction')) {
        let actionLine = '';
        if (currentApiMethod === 'GET') {
            actionLine = `        response = await request.get('${currentApiUrl}');`;
        } else if (currentApiMethod === 'POST') {
            actionLine = `        response = await request.post('${currentApiUrl}', { data: {} });`;
        } else if (currentApiMethod === 'PUT') {
            actionLine = `        response = await request.put('${currentApiUrl}', { data: {} });`;
        } else if (currentApiMethod === 'DELETE') {
            actionLine = `        response = await request.delete('${currentApiUrl}');`;
        }
        line = actionLine;
    }

    // Replace the generic page.locator verify with response validation
    if (line.includes("expect(page.locator('body')).toBeVisible();")) {
        line = `        expect(response).toBeDefined();\n        expect(response.status()).toBeLessThan(500);`;
    }

    outLines.push(line);
}

fs.writeFileSync(file, outLines.join('\n'), 'utf-8');
console.log('API tests in scenario 8 patched successfully!');
