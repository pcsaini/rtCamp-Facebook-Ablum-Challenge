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
include 'zipper.php';

$fb->setDefaultAccessToken($_SESSION['fb_access_token']);
$res = $fb->get( '/me?fields=first_name,last_name' );
$user = $res->getGraphObject();
$username = $user->getProperty('first_name').'_'.$user->getProperty('last_name').'_albums';

$download_location = 'downloads/'.$username.'/';
mkdir($download_location, 0777);

function url_get_contents ($Url) {
    if (!function_exists('curl_init')){
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function allAlbumDownload(){
    global $fb;
    try {
        $response = $fb->get('/me/albums?fields=id,name');
        $albums = $response->getGraphEdge();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    foreach ($albums as $album) {
        downloadAlbum($album['id']);
    }
}

function downloadAlbum($album_id){
    global $fb;
    global $download_location;
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
    $response = $fb->get('/'.$album_id.'?fields=id,name');
    $album = $response->getGraphObject();

    $album_location = $download_location.$album['name'];

    if (!file_exists($album_location)) {
        mkdir($album_location, 0777);
    }

    do{
        foreach ($photos as $photo) {
            file_put_contents( $album_location.'/'.uniqid().".jpg", url_get_contents( $photo['source']) );
        }
        $photos = $fb->next($photos);
    }while(!is_null($photos));
}

function make_zip()
{
    global $download_location;
    require_once('zipper.php');
    $zipper = new zipper();
    echo $zipper->get_zip($download_location);
}

if (isset($_POST['download_all'])){
    allAlbumDownload();
    make_zip();
}

if (isset($_POST['download_selected'])){
    $album_ids = explode("-", $_POST['albums']);
    foreach ( $album_ids as $album_id ) {
        $multiple = explode( ",", $album_id );
        downloadAlbum($album_id);
    }
    make_zip();
}

if (isset($_POST['download_single'])){
    $album_id = $_POST['album_id'];
    downloadAlbum($album_id);
    make_zip();
}