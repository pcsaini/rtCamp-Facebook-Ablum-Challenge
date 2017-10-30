<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 29/10/17
 * Time: 12:51 PM
 */

if (!session_id()) {
    session_start();
}


include 'config.php';
$fb->setDefaultAccessToken($_SESSION['fb_access_token']);

if (isset($_POST['photos'])){
    $album_id = $_POST['album_id'];

    try {
        $photos_request = $fb->get('/'.$album_id.'/photos?fields=source');
        $photos = $photos_request->getGraphEdge();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    $count = 0;
    foreach ($photos as $photo){

        $response[$count]['src'] = $photo['source'];
        $response[$count]['thumb'] = $photo['source'];
        $count++;
    }
    echo json_encode($response);
}