<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 29/10/17
 * Time: 1:22 PM
 */

if (!session_id()) {
    session_start();
}

include 'config.php';

$helper = $fb->getRedirectLoginHelper();

$fbLogoutUrl = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'http://localhost/rtcamp/index.php');
$client->revokeToken();
session_destroy();
unset($_SESSION['access_token']);
unset($_SESSION['fb_access_token']);
header("Location: $fbLogoutUrl");
exit;