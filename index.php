<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 29/10/17
 * Time: 10:52 AM
 */

if (!session_id()) {
    session_start();
}
if (isset($_SESSION['fb_access_token'])) {
    header('location: http://localhost/rtcamp/facebook.php');
    exit;
}

include "config.php";
$helper = $fb->getRedirectLoginHelper();
$permissions = array('email', 'user_photos');
$loginUrl = $helper->getLoginUrl($redirect, $permissions);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="apple-touch-icon" href="img/facebook.png">
    <title>Facebook Album</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Facebook Album Gallery</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo $loginUrl; ?>">Login With Facebook</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="text-center">Facebook Album Gallery</h1>
    <div class="text-center">
        <img src="img/facebook.png" style="border-radius: 50%;height: 200px;width: 200px;">
    </div>
</div>

<br>
<br>

<div class="text-center">
    <a href="<?php echo $loginUrl; ?>" class="btn btn-primary btn-lg">
        <i class="fa fa-facebook"></i> Login with Facebook
    </a>
</div>

<footer>
    <div class="footer-bottom">
        <div class="container">
            <div class="text-center"> Copyright © 2017 <a href="https://www.pcsaini" target="_blank">Prem Chand Saini
                    (pcsaini)</a>.  All right reserved.
            </div>
        </div>
    </div>
</footer>

<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>