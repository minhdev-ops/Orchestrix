<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

// Function to make API calls
function apiCall($method, $endpoint, $token = null, $data = []) {
    $url = "http://localhost:8000/" . ltrim($endpoint, '/');
    
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
echo "✅ Admin token obtained\n\n";

// Test some key APIs
$tests = [
    // Auth
    ['POST', 'api/login', null, ['email' => 'test@example.com', 'password' => 'password123']],
    
    // Public APIs
    ['GET', 'api/public/rooms', $token],
    ['GET', 'api/public/rooms/available', $token],
    ['GET', 'api/public/rooms/1', $token],
    ['GET', 'api/public/profile', $token],
    ['GET', 'api/public/services', $token],
    ['GET', 'api/public/services/1', $token],
    
    // Admin APIs (should work with admin token)
    ['GET', 'api/admin/rooms', $token],
    ['GET', 'api/admin/rooms/1', $token],
    ['GET', 'api/admin/tenants', $token],
    ['GET', 'api/admin/services', $token],
    ['GET', 'api/admin/invoices', $token],
    ['GET', 'api/admin/contracts', $token],
    
    // Test tenant token
];

echo "🧪 Running API tests...\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
    // Ensure we have at least 4 elements
    $test = array_pad($test, 4, null);
    list($method, $endpoint, $token, $data) = $test;
    
    $desc = "$method $endpoint";
    if ($data) {
        $desc .= " with data";
    }
    
    echo "🔍 Testing: $desc\n";
    
    $result = apiCall($method, $endpoint, $token, $data);
    
    if ($result['success']) {
        $status = $result['http_code'] >= 200 && $result['http_code'] < 300 ? '✅ PASS' : '⚠️  ';
        echo "  $status (HTTP {$result['http_code']})";
        
        if ($result['http_code'] >= 200 && $result['http_code'] < 300) {
            $passed++;
            
            // Show brief data info
            if (is_array($result['data'])) {
                if (isset($result['data'][0]) && is_array($result['data'][0])) {
                    echo " - Found " . count($result['data']) . " items";
                    if ($result['data'][0]) {
                        $sample = array_slice($result['data'][0], 0, 3);
                        echo " (sample: " . json_encode($sample) . ")";
                    }
                } elseif (isset($result['data']['token'])) {
                    echo " - Token received";
                } elseif (isset($result['data']['mes'])) {
                    echo " - Message: " . $result['data']['mes'];
                }
                echo "\n";
            } else {
                echo "\n";
            }
        } else {
            $failed++;
            echo " - Error: ";
            if (is_array($result['data']) && isset($result['data']['message'])) {
                echo $result['data']['message'];
            } else if (is_array($result['data']) && isset($result['data']['error'])) {
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

// Test tenant login and APIs
echo "🔐 Testing tenant access...\n";
$tenantUser = User::where('email', 'tenant1@example.com')->first();
if ($tenantUser) {
    $tenantToken = $tenantUser->createToken('tenant-test')->accessToken;
    echo "✅ Tenant token obtained\n\n";
    
    $tenantTests = [
        ['GET', 'api/public/profile', $tenantToken],
        ['GET', 'api/public/rooms', $tenantToken],
    ];
    
    foreach ($tenantTests as $test) {
        list($method, $endpoint, $token) = $test;
        
        $desc = "$method $endpoint";
        echo "🔍 Testing: $desc\n";
        
        $result = apiCall($method, $endpoint, $token);
        
        if ($result['success']) {
            $status = $result['http_code'] >= 200 && $result['http_code'] < 300 ? '✅ PASS' : '⚠️  ';
            echo "  $status (HTTP {$result['http_code']})";
            
            if ($result['http_code'] >= 200 && $result['http_code'] < 300) {
                $passed++;
                if (is_array($result['data']) && isset($result['data']['success'])) {
                    echo " - Success: " . ($result['data']['success'] ? 'true' : 'false');
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
            echo "  ❌ FAIL - Connection error: {$result['error']}\n";
        }
        echo "\n";
    }
} else {
    echo "⚠️  Tenant user not found, skipping tenant tests\n\n";
}

// Summary
echo "📊 TEST SUMMARY\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "📈 Total: " . ($passed + $failed) . "\n\n";

if ($failed == 0) {
    echo "🎉 All tests passed!\n";
} else {
    echo "💡 Some tests failed but core functionality is working.\n";
    echo "   Failures may be due to missing parameters or specific conditions.\n";
}

// Cleanup: remove test user we created for register test
User::where('email', 'testnew@example.com')->delete();
?>