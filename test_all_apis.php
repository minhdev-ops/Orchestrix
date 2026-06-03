<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

// Function to make API calls
function apiCall($method, $endpoint, $token = null, $data = []) {
    $url = "http://localhost:8000/api/" . ltrim($endpoint, '/');
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = [
        "Accept: application/json",
        "Content-Type: application/json",
    ];
    
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    if ($curlError) {
        return [
            'success' => false,
            'error' => $curlError,
            'http_code' => $httpCode,
            'data' => null
        ];
    }
    
    return [
        'success' => true,
        'http_code' => $httpCode,
        'data' => json_decode($response, true),
        'raw_response' => $response
    ];
}

// Get admin token
echo "🔐 Getting admin token...\n";
$adminUser = User::where('email', 'test@example.com')->first();
if (!$adminUser) {
    die("❌ Admin user not found! Please run seeders first.\n");
}
$adminUser->role = 'admin';
$adminUser->save();
$token = $adminUser->createToken('test-token')->accessToken;
echo "✅ Admin token obtained: " . substr($token, 0, 20) . "...\n\n";

// Load routes from extracted_api_routes.json
echo "📋 Loading API routes...\n";
$routesJson = file_get_contents(__DIR__ . '/extracted_api_routes.json');
$routes = json_decode($routesJson, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("❌ Failed to parse routes JSON: " . json_last_error_msg() . "\n");
}

echo "✅ Loaded " . count($routes) . " route definitions\n\n";

// Group routes by method and endpoint for cleaner testing
$endpointGroups = [];
foreach ($routes as $route) {
    $uri = $route['uri'];
    $methods = explode('|', $route['method']);
    
    // Clean up URI for testing (remove Laravel parameter syntax)
    $testUri = preg_replace('/\{[^}]+\}/', '1', $uri); // Replace {param} with 1
    $testUri = ltrim($testUri, '/');
    
    foreach ($methods as $method) {
        $method = trim(strtoupper($method));
        if (!isset($endpointGroups[$testUri])) {
            $endpointGroups[$testUri] = [];
        }
        if (!in_array($method, $endpointGroups[$testUri])) {
            $endpointGroups[$testUri][] = $method;
        }
    }
}

// Remove duplicates and sort
ksort($endpointGroups);

echo "🧪 Starting API tests...\n";
echo "=" . str_repeat("=", 60) . "\n\n";

$passed = 0;
$failed = 0;

// Test each endpoint
foreach ($endpointGroups as $endpoint => $methods) {
    // Skip certain endpoints that need special handling
    $skipPatterns = [
        '/{id}/contracts',
        '/{id}/tenants', 
        '/{id}/pdf',
        '/{id}/pay',
        '/payment/history',
        '/active',
        '/expired',
        '/my',
        '/subscribe',
        // Webhook and auth endpoints we'll test separately
        'webhook/',
        'login',
        'register',
        'forget-pass',
        'activeMail',
        'reActive',
        'resetPass',
        'changePass',
        'logout',
        'show',
        'oauth'
    ];
    
    $shouldSkip = false;
    foreach ($skipPatterns as $pattern) {
        if (strpos($endpoint, $pattern) !== false) {
            $shouldSkip = true;
            break;
        }
    }
    
    if ($shouldSkip) {
        continue;
    }
    
    echo "🔍 Testing endpoint: /api/$endpoint\n";
    
    // Try each method for this endpoint
    $endpointPassed = false;
    foreach ($methods as $method) {
        // Skip GET if we already tested it with another method for simplicity
        if ($method === 'GET' && isset($testedMethods[$endpoint]) && in_array('GET', $testedMethods[$endpoint])) {
            continue;
        }
        
        $testedMethods[$endpoint][] = $method;
        
        $result = apiCall($method, $endpoint, $token);
        
        if ($result['success']) {
            $status = $result['http_code'] >= 200 && $result['http_code'] < 300 ? '✅ PASS' : '⚠️  ';
            echo "  $method: $status (HTTP {$result['http_code']})";
            
            if ($result['http_code'] >= 200 && $result['http_code'] < 300) {
                $passed++;
                $endpointPassed = true;
                
                // Show sample data for successful responses
                if (is_array($result['data']) && isset($result['data'])) {
                    if (isset($result['data'][0]) && is_array($result['data'][0])) {
                        echo " - Found " . count($result['data']) . " items";
                        if (count($result['data']) > 0) {
                            $firstItem = $result['data'][0];
                            $keys = array_keys($firstItem);
                            echo " (fields: " . implode(', ', array_slice($keys, 0, 3)) . (count($keys) > 3 ? ",..." : "") . ")";
                        }
                    } elseif (is_array($result['data'])) {
                        echo " - Response with " . count($result['data']) . " fields";
                    }
                }
                echo "\n";
            } else {
                $failed++;
                echo " - Error: ";
                if (is_array($result['data']) && isset($result['data']['message'])) {
                    echo $result['data']['message'];
                } else {
                    echo "HTTP {$result['http_code']}";
                }
                echo "\n";
            }
        } else {
            $failed++;
            echo "  $method: ❌ FAIL - Connection error: {$result['error']}\n";
        }
    }
    
    if (!$endpointPassed && !empty($methods)) {
        echo "  All methods failed for this endpoint\n";
    }
    
    echo "\n";
}

// Test auth endpoints separately (they don't need the bearer token in the same way)
echo "🔐 Testing Auth endpoints...\n";
echo "-" . str_repeat("-", 40) . "\n";

$authTests = [
    ['POST', 'login', ['email' => 'test@example.com', 'password' => 'password123']],
    ['POST', 'register', ['email' => 'testnew@example.com', 'password' => 'password123', 'name' => 'Test New', 'repass' => 'password123']],
];

foreach ($authTests as [$method, $endpoint, $data]) {
    echo "🔍 Testing $method /api/$endpoint\n";
    $result = apiCall($method, $endpoint, null, $data);
    
    if ($result['success']) {
        $status = $result['http_code'] >= 200 && $result['http_code'] < 300 ? '✅ PASS' : '⚠️  ';
        echo "  $status (HTTP {$result['http_code']})";
        
        if ($result['http_code'] >= 200 && $result['http_code'] < 300) {
            $passed++;
            if (is_array($result['data']) && isset($result['data']['token'])) {
                echo " - Token received\n";
            } elseif (is_array($result['data']) && isset($result['data']['mes'])) {
                echo " - Message: " . $result['data']['mes'] . "\n";
            } else {
                echo "\n";
            }
        } else {
            $failed++;
            echo " - Error: ";
            if (is_array($result['data']) && isset($result['data']['error'])) {
                echo $result['data']['error'];
            } else {
                echo "HTTP {$result['http_code']}";
            }
            echo "\n";
        }
    } else {
        $failed++;
        echo "  ❌ FAIL - Connection error: {$result['error']}\n";
    }
    echo "\n";
}

// Summary
echo "📊 TEST SUMMARY\n";
echo "=" . str_repeat("=", 60) . "\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "📈 Total: " . ($passed + $failed) . "\n";

if ($failed == 0) {
    echo "🎉 All tests passed!\n";
} else {
    echo "⚠️  Some tests failed. Check the output above for details.\n";
}

// Cleanup: remove test user we created for register test
User::where('email', 'testnew@example.com')->delete();
?>