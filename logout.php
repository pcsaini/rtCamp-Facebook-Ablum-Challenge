<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 29/10/17
 * Time: 1:22 PM
 */
$fbLogoutUrl = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'http://localhost/rtcamp/index.php');
session_destroy();
unset($_SESSION['access_token']);
header("Location: $fbLogoutUrl");
exit;