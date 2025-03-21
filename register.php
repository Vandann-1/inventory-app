<?php
session_start();
require 'api.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];

    $response = sendRequestToDjango('register/', [
        'email' => $email,
        'full_name' => $full_name,
        'password' => $password
    ]);

    //For Debugging
    echo "<pre>";
    print_r($response);
    echo "</pre>";

    if (isset($response['token'])) {
        $_SESSION['token'] = $response['token'];
        echo "Register Successful! <a href='protected.php'>Access Protected Data</a>";
    } else {
        echo "Register Failed: " . $response['message'];
    }
}
?>

<form method="post">
    <?php echo $response['message'];?>
    Full Name: <input type="text" name="full_name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="register">
</form>
