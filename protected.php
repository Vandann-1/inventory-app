<?php
session_start();
include 'api.php';

if (!isset($_SESSION['token'])) {
    echo "You need to log in first. <a href='login.php'>Login</a>";
    exit;
}

$token = $_SESSION['token'];
$response = sendRequestToDjango('protected/', [], $token, 'GET');

if (isset($response['error'])) {
    echo "Error: " . $response['error'];
} else {
    echo "Protected Data: " . json_encode($response);
}
?>
