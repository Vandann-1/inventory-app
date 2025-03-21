<?php
session_start();
require 'api.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $response = sendRequestToDjango('login/', [
        'email' => $email,
        'password' => $password
    ]);

    //For Debugging
    echo "<pre>";
    print_r($response);
    echo "</pre>";

    if (isset($response['token'])) {
        $_SESSION['token'] = $response['token'];
        $msg  = "<div id='success-form'>Login Successful! <a href='protected.php'>Access Protected Data</a></div>";
        header('location: register.php');
    } else {
        $msg = $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | TS</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/imgs/favicon.png">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/all.css">
</head>

<body class="login">
  <!-------- Login form -------->
  <div class="container-login">
    <?php if (isset($msg)) {
      echo $msg;
    } ?><br>
    <div class="wrapper">
      <div class="title"><span>Techspire Solutions</span></div>
      <form method="POST">
        <div class="row">
          <i class="fas fa-user"></i>
          <input type="text" id="email" name="email" placeholder="Email" required autofocus>
        </div>
        <div class="row">
          <i class="fas fa-lock"></i>
          <input type="password" class="password" id="password" name="password" placeholder="Password" required>
        </div>
        <div class="row button">
          <input type="submit" name="submit" value="Login"><br>
        </div>
      </form>
    </div>
  </div>
  <script>
    /*================================ To remove error msg ==================================*/
    document.addEventListener('DOMContentLoaded', function() { // Wait for the document to fully load
      var sessionMsg = document.getElementById('error'); // Find the session message element
      if (sessionMsg) { // If the session message element exists, set a timeout to remove it
        setTimeout(function() {
          sessionMsg.style.display = 'none';
        }, 10000); // 10000 milliseconds = 10 seconds
      }
    });
  </script>
</body>

</html>
