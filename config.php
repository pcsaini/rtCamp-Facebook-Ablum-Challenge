<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 29/10/17
 * Time: 10:56 AM
 */

require_once __DIR__ . '/lib/facebook/vendor/autoload.php';
require_once 'lib/google/vendor/autoload.php';

//FB Config
$app_id = '171027806813059';
$app_secret = '2b77af3bd844aa475a450d9642b2efce';
//$redirect = "https://rtcamp-facebook-album.herokuapp.com/facebook.php";
$redirect = "http://localhost/rtcamp/facebook.php";

$fb = new Facebook\Facebook([
    'app_id' => $app_id,
    'app_secret' => $app_secret,
    'default_graph_version' => 'v2.10',
    'persistent_data_handler'=>'session'
]);


//Google Config
//$google_redirect_url = "https://rtcamp-facebook-album.herokuapp.com/googleAuth.php";
$google_redirect_url = "http://localhost/rtcamp/googleAuth.php";
$client = new Google_Client();
$client->setAuthConfigFile('google.json');
$client->setRedirectUri($google_redirect_url);
$client->addScope(Google_Service_Drive::DRIVE);