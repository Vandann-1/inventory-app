<?php
session_start();
include 'api.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $response = sendRequestToDjango('login/', [
        'username' => $username,
        'password' => $password
    ]);

    echo "<pre>";
    print_r($response);
    echo "</pre>";

    if (isset($response['token'])) {
        $_SESSION['token'] = $response['token'];
        echo "Login Successful! <a href='protected.php'>Access Protected Data</a>";
    } else {
        echo "Login Failed: " . $response['message'];
    }
}
?>

<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
