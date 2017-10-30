<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 29/10/17
 * Time: 12:49 PM
 */

session_start();

include 'config.php';

if (! isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    echo '<script type="text/javascript">window.close();</script>';
}
