<?php
// COMPREHENSIVE DEBUG SCRIPT
// Akses: http://localhost/trevio-project/public/test-admin-routing.php

echo "<style>
    body { font-family: Arial; margin: 20px; }
    .success { color: green; background: #e8f5e9; padding: 10px; margin: 10px 0; border-left: 4px solid green; }
    .error { color: red; background: #ffebee; padding: 10px; margin: 10px 0; border-left: 4px solid red; }
    .info { color: #1976d2; background: #e3f2fd; padding: 10px; margin: 10px 0; border-left: 4px solid #1976d2; }
    .warning { color: #f57f17; background: #fff3e0; padding: 10px; margin: 10px 0; border-left: 4px solid #f57f17; }
    code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
    h2 { border-bottom: 2px solid #1976d2; }
</style>";

echo "<h1>🔍 ADMIN ROUTING DEBUG</h1>";

// Ensure session
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if test user role is set
if (!isset($_SESSION['user_role'])) {
    echo "<div class='warning'><strong>⚠️ TEST MODE:</strong> Simulating admin login for testing...</div>";
    $_SESSION['user_id'] = 999;
    $_SESSION['user_name'] = 'Test Admin';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['user_email'] = 'admin@test.local';
}

echo "<h2>1️⃣ SESSION INFO</h2>";
echo "<div class='info'>";
echo "<strong>user_id:</strong> <code>" . ($_SESSION['user_id'] ?? 'NOT SET') . "</code><br>";
echo "<strong>user_role:</strong> <code>" . ($_SESSION['user_role'] ?? 'NOT SET') . "</code><br>";
echo "<strong>user_name:</strong> <code>" . ($_SESSION['user_name'] ?? 'NOT SET') . "</code><br>";
if ($_SESSION['user_role'] === 'admin') {
    echo "<div class='success'>✅ User has ADMIN role</div>";
} else {
    echo "<div class='error'>❌ User DOES NOT have admin role</div>";
}
echo "</div>";

// Load app files
require_once '../app/init.php';

echo "<h2>2️⃣ CONFIG CHECK</h2>";
echo "<div class='info'>";
echo "<strong>BASE_URL:</strong> <code>" . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED') . "</code><br>";
echo "<strong>APP_DEBUG:</strong> <code>" . (defined('APP_DEBUG') ? (APP_DEBUG ? 'true' : 'false') : 'NOT DEFINED') . "</code><br>";
echo "</div>";

echo "<h2>3️⃣ CONTROLLER FILE CHECKS</h2>";
$controllers = [
    'AdminHotelController' => '../app/controllers/AdminHotelController.php',
    'AdminHotelsController' => '../app/controllers/AdminHotelsController.php',
];

foreach ($controllers as $name => $path) {
    if (file_exists($path)) {
        echo "<div class='success'>✅ <code>$name</code> EXISTS</div>";
    } else {
        echo "<div class='error'>❌ <code>$name</code> NOT FOUND: <code>$path</code></div>";
    }
}

echo "<h2>4️⃣ ROUTE SIMULATION</h2>";
echo "<div class='info'>";

// Simulate App URL parsing
$_SERVER['REQUEST_URI'] = '/trevio-project/public/admin/hotels';
$_SERVER['SCRIPT_NAME'] = '/trevio-project/public/index.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$url = trim(substr($requestUri, strlen($scriptName)), '/');
$urlSegments = explode('/', $url);

echo "<strong>REQUEST_URI:</strong> <code>" . $_SERVER['REQUEST_URI'] . "</code><br>";
echo "<strong>SCRIPT_NAME:</strong> <code>" . $_SERVER['SCRIPT_NAME'] . "</code><br>";
echo "<strong>Script Dir:</strong> <code>$scriptName</code><br>";
echo "<strong>Parsed URL:</strong> <code>$url</code><br>";
echo "<strong>URL Segments:</strong> <pre>";
print_r($urlSegments);
echo "</pre>";

// Test routing logic
echo "<strong>Routing Logic Test:</strong><br>";
if (isset($urlSegments[0]) && $urlSegments[0] === 'admin' && isset($urlSegments[1])) {
    $resource = $urlSegments[1];
    $singular = rtrim($resource, 's');
    $controllerName = 'Admin' . ucfirst($singular) . 'Controller';
    echo "✓ Admin route detected<br>";
    echo "✓ Resource: <code>$resource</code><br>";
    echo "✓ Singular: <code>$singular</code><br>";
    echo "✓ Generated Controller: <code>$controllerName</code><br>";
    
    if (class_exists("\\App\\Controllers\\" . $controllerName)) {
        echo "<div class='success'>✅ Controller class FOUND</div>";
    } else {
        echo "<div class='error'>❌ Controller class NOT FOUND</div>";
    }
}

echo "</div>";

echo "<h2>5️⃣ ADMIN BASE CONTROLLER CHECK</h2>";
if (class_exists("\\App\\Controllers\\BaseAdminController")) {
    echo "<div class='success'>✅ BaseAdminController EXISTS</div>";
    
    // Check if it has requireAdminLogin method
    $reflection = new ReflectionClass("\\App\\Controllers\\BaseAdminController");
    $methods = $reflection->getMethods();
    $methodNames = array_map(fn($m) => $m->getName(), $methods);
    
    if (in_array('requireAdminLogin', $methodNames)) {
        echo "<div class='success'>✅ requireAdminLogin method EXISTS</div>";
    } else {
        echo "<div class='error'>❌ requireAdminLogin method NOT FOUND</div>";
    }
} else {
    echo "<div class='error'>❌ BaseAdminController NOT FOUND</div>";
}

echo "</div>";

echo "<h2>6️⃣ RECOMMENDATIONS</h2>";
echo "<div class='info'>";
echo "✓ If all checks pass, try accessing: <a href='" . BASE_URL . "/admin/hotels' target='_blank'><code>/admin/hotels</code></a><br>";
echo "✓ If you see redirect to home, check:<br>";
echo "  - Browser console for JavaScript errors<br>";
echo "  - Check if <code>BASE_URL</code> is being set correctly in dashboard.php<br>";
echo "  - Verify session is persistent across requests<br>";
echo "</div>";

echo "<p style='margin-top: 30px; color: #999; font-size: 12px;'>Debug generated on " . date('Y-m-d H:i:s') . "</p>";
?>
