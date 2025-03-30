<?php
ini_set('display_errors', 'Off'); // Not to show errors on page
session_start();
require 'api.php';

// Check Admin_Login session
if (isset($_SESSION['Admin_Login']) && isset($_SESSION['token']) && $_SESSION['Admin_Login'] != '' && $_SESSION['Admin_Login'] == 'yes') {
  // Redirect to login page
  header('Location: index');
  exit();
}

$response = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  try {
      $response = sendRequestToDjango('login/', [
          'email' => $email,
          'password' => $password
      ]);

      if ($response === false || empty($response)) {
          throw new Exception("Server is not responding. Please try again later.");
      }

      if (isset($response['token'])) {
          $_SESSION['token'] = $response['token'];
          $user_data = $response['user'];
          $_SESSION['username'] = $user_data['username'];
          $_SESSION['role'] = $user_data['role'];
          $_SESSION['Admin_Login'] = "yes";
          header('location: index');
          exit();
      } else {
          $msg = "<div id='error'><i class='fa-regular fa-circle-exclamation'></i> " . htmlspecialchars($response['message'] ?? "An error occurred.") . "</div>";
      }
  } catch (Exception $e) {
      $msg = "<div id='error'><i class='fa-regular fa-circle-exclamation'></i> " . htmlspecialchars($e->getMessage()) . "</div>";
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
  <link rel="stylesheet" href="assets/css/style-login.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/all.css">
</head>

<body class="login">
  <!-------- Login form -------->
  <div class="container-login">
    <?php 
    if (isset($msg)) {
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
