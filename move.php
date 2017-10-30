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
ini_set('max_execution_time', 300);
if (!isset($_SESSION['fb_access_token'] )) {
    header('location: index.php');
    exit;
}

include 'config.php';
$fb->setDefaultAccessToken($_SESSION['fb_access_token']);

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


function moveToDrive($album_id,$folderId,$drive){
    global $fb;
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

    $fileMetadata = new Google_Service_Drive_DriveFile(array(
        'name' => $album['name'],
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => array($folderId)
    ));
    $file = $drive->files->create($fileMetadata, array('fields' => 'id'));
    $album_folder = $file->id;

    do{
        foreach ($photos as $photo) {
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => uniqid().'.jpg',
                'parents' => array($album_folder)
            ));
            //$content = file_get_contents($photo['source'] );
            $content = url_get_contents($photo['source'] );
            $file = $drive->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => 'image/jpeg',
                'uploadType' => 'multipart',
                'fields' => 'id'));
        }
        $photos = $fb->next($photos);
    }while(!is_null($photos));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    $client->setAccessToken($_SESSION['access_token']);
    if ($client->isAccessTokenExpired()) {
        echo "Session Expired. Logout and Login Again to Google";
        echo '<script type="text/javascript">window.open("http://localhost/rtcamp/googleAuth.php", "Drive Access", width="700", height="380");</script>';
        exit;
    }
    $drive = new Google_Service_Drive($client);

    // username field is deprecated in graph API for versions v2.0 and higher
    $res = $fb->get( '/me?fields=first_name,last_name' );
    $user = $res->getGraphObject();
    $username = 'facebook_'.$user->getProperty('first_name').'_'.$user->getProperty('last_name').'_albums';

    $fileMetadata = new Google_Service_Drive_DriveFile(array(
        'name' => $username,
        'mimeType' => 'application/vnd.google-apps.folder'));
    $file = $drive->files->create($fileMetadata, array('fields' => 'id'));
    $folderId = $file->id;

    if(isset($_POST['move_single'])) {
        $album_id = $_POST['album_id'];
        moveToDrive($album_id,$folderId,$drive);
        echo '<div class="text-center text-success">
                    <i class="fa fa-check-circle fa-3x"></i>
                    <br>
                    <h3 class="text-success">Success!</h3>
                </div>';
    }

    if(isset($_POST['move_selected'])) {
        $album_ids = explode("-", $_POST['albums']);
        foreach ( $album_ids as $album_id ) {
            $multiple = explode( ",", $album_id );
            moveToDrive($album_id,$folderId,$drive);
        }
        echo '<div class="text-center text-success">
                    <i class="fa fa-check-circle fa-3x"></i>
                    <br>
                    <h3 class="text-success">Success!</h3>
                </div>';
    }

    if(isset($_POST['move_all'])) {
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
            moveToDrive($album['id'],$folderId,$drive);
        }
        echo '<div class="text-center text-success">
                    <i class="fa fa-check-circle fa-3x"></i>
                    <br>
                    <h3 class="text-success">Success!</h3>
                </div>';
    }

} else {
    echo "Please Login First";
    echo '<script type="text/javascript">window.open("http://localhost/rtcamp/googleAuth.php", "Drive Access", width="400", height="400");</script>';
}
