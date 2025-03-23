<?php
session_start();
if(isset($_SESSION['Admin_Login']) && isset($_SESSION['token']) && $_SESSION['Admin_Login'] !='' && $_SESSION['Admin_Login'] == 'yes'){
    unset($_SESSION['Admin_Login']);
    unset($_SESSION['token']);
    header('location: login');
}else{
    header('location: login');
    die();
}
?>