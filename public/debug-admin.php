<?php
// Debug script untuk check routing dan session admin
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../app/init.php';

echo "<h2>DEBUG INFO</h2>";
echo "<h3>SESSION:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>URL PARSING:</h3>";
$app = new App\Core\App();
// Don't execute, just check the parseUrl function
$reflection = new ReflectionClass('App\Core\App');
$parseUrl = $reflection->getMethod('parseUrl');
$parseUrl->setAccessible(true);

$testApp = new stdClass();
$url = $parseUrl->invoke($testApp);
echo "Parsed URL: ";
print_r($url);

echo "<h3>REQUEST INFO:</h3>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
?>
