<?php
// Buat file check_token.php di root folder

require __DIR__ . '/vendor/autoload.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $parts = explode('.', $token);

    if (count($parts) != 3) {
        echo "Invalid token format";
        exit;
    }

    // Decode header
    $header = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[0])), true);
    echo "<h3>Header:</h3>";
    echo "<pre>" . print_r($header, true) . "</pre>";

    // Decode payload
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
    echo "<h3>Payload:</h3>";
    echo "<pre>" . print_r($payload, true) . "</pre>";

    // Check expiration
    if (isset($payload['exp'])) {
        $exp = $payload['exp'];
        $now = time();
        echo "<h3>Expiration:</h3>";
        echo "Token expires at: " . date('Y-m-d H:i:s', $exp) . "<br>";
        echo "Current time: " . date('Y-m-d H:i:s', $now) . "<br>";
        echo "Status: " . ($now > $exp ? "EXPIRED" : "VALID") . "<br>";
        echo "Time remaining: " . ($exp - $now) . " seconds<br>";
    }
} else {
    echo "<form method='get'>";
    echo "<textarea name='token' rows='10' cols='100'></textarea><br>";
    echo "<input type='submit' value='Check Token'>";
    echo "</form>";
}
