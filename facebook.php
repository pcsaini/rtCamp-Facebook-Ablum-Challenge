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

include 'config.php';

$helper = $fb->getRedirectLoginHelper();

if (!isset($_SESSION['fb_access_token'])) {

    try {
        $accessToken = $helper->getAccessToken();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    if (!isset($accessToken)) {
        if ($helper->getError()) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Error: " . $helper->getError() . "\n";
            echo "Error Code: " . $helper->getErrorCode() . "\n";
            echo "Error Reason: " . $helper->getErrorReason() . "\n";
            echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Bad request';
        }
        exit;
    }

    // The OAuth 2.0 client handler helps us manage access tokens
    $oAuth2Client = $fb->getOAuth2Client();

    // Get the access token metadata from /debug_token
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);

    // Validation (these will throw FacebookSDKException's when they fail)
    $tokenMetadata->validateAppId($app_id);
    $tokenMetadata->validateExpiration();

    if (!$accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
            exit;
        }
    }

    $_SESSION['fb_access_token'] = (string)$accessToken;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="apple-touch-icon" href="">
    <title>Facebook Album</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/lightgallery.min.css">
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
            <a class="navbar-brand" href="#">Facebook Album</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            $fb->setDefaultAccessToken($_SESSION['fb_access_token']);

            // Get User Details
            $res = $fb->get('/me?fields=name,gender,email');
            $user = $res->getGraphObject();
            ?>
            <div class="text-center">
                <img src="<?php echo 'https://graph.facebook.com/' . $user->getProperty('id') . '/picture?type=normal' ?>"
                     style="border-radius: 50%;height: 100px;width: 100px;">
                <p>Welcome, <strong><?php echo $user->getProperty('name'); ?></strong></p>
                <p>Email: <strong><?php echo $user->getProperty('email'); ?></strong></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <h3><?php echo $user->getProperty('name'); ?>'s Facebook Albums</h3>
        </div>
        <div class="col-md-12 text-center">
            <button class="btn btn-primary btn-round" id="download_all">Download All</button>
            <button class="btn btn-primary btn-round" id="download_selected">Download Selected</button>
            <button class="btn btn-primary btn-round" id="move_all">Move All</button>
            <button class="btn btn-primary btn-round" id="move_selected">Move Selected</button>
        </div>
    </div>
    <div class="row">
        <?php
        try {
            //Get User Album
            $response = $fb->get('/me/albums?fields=id,name,cover_photo,count,type,created_time,from,link');
            $albums = $response->getGraphEdge();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        foreach ($albums as $album) { ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="album">
                    <a class="dynamic" data-id="<?php echo $album['id']; ?>">
                        <?php
                        if (isset($album['cover_photo'])) {
                            $cover_photo_id = $album['cover_photo']['id'];

                            try {
                                $photos_request = $fb->get('/' . $album['id'] . '/photos?fields=images');
                                $photos = $photos_request->getGraphEdge();
                            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                                echo 'Graph returned an error: ' . $e->getMessage();
                                exit;
                            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                                exit;
                            }
                            $album_cover_photo = '';
                            foreach ($photos as $photo) {
                                if ($cover_photo_id == $photo['id']) {
                                    $is_cover_photo = True;
                                    $images = $photo['images'];
                                    foreach ($images as $image) {
                                        if ($image['width'] == 320 || $image['height'] == 320) {
                                            $album_cover_photo = $image['source'];
                                        }
                                    }

                                }
                            }

                            echo '<div class="album_img" style=\'background-image: url("' . $album_cover_photo . '")\'></div>';
                        } else {
                            echo '<div class="album_img" style=\'background-image: url("img/blank.png")\'></div>';
                        }
                        ?>
                    </a>

                    <a class="dynamic" data-id="<?php echo $album['id']; ?>"><h3><?php echo $album['name'] . ' (' . $album['count'] . ')'; ?></h3></a>
                    <div class="content">
                        <div class="toggler">
                            <input id="toggler-<?php echo $album['id']; ?>" name="toggler-1" class="check"
                                   type="checkbox" value="<?php echo $album['id']; ?>"/>
                            <label for="toggler-<?php echo $album['id']; ?>">
                                <svg class="toggler-on" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                     viewbox="0 0 130.2 130.2">
                                    <polyline class="path check" points="100.2,40.2 51.5,88.8 29.8,67.5 "></polyline>
                                </svg>
                                <svg class="toggler-off" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                     viewbox="0 0 130.2 130.2">
                                    <line class="path line" x1="34.4" y1="34.4" x2="95.8" y2="95.8"></line>
                                    <line class="path line" x1="95.8" y1="34.4" x2="34.4" y2="95.8"></line>
                                </svg>
                            </label>
                        </div>
                        <!--<a class="btn btn-primary"><input type="checkbox" class="check" value="<?php /*echo $album['id']; */ ?>">
                        <label for="box-1"> Select</label></a>-->
                        <div class="action">
                            <button class="btn btn-primary btn-round download_album"
                                    data-id="<?php echo $album['id']; ?>">Download
                            </button>
                            <button class="btn btn-primary btn-round move_album" data-id="<?php echo $album['id']; ?>">
                                Move to Drive
                            </button>
                        </div>

                    </div>

                </div>
            </div>

            <?php
        }
        ?>
    </div>
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

<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Albums Notification</h4>
            </div>
            <div class="modal-body" id="responseDisplay">
                <!-- Download Response -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="moveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Notification</h4>
            </div>
            <div class="modal-body" id="responseDisplay1">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/showDialog.js"></script>
<script src="js/lightGallery/lightgallery-all.min.js"></script>
<script src="js/main.js"></script>

<script>
    $(document).ready(function () {
        $('.dynamic').click(function () {
            var album_id = $(this).data('id');

            $.ajax({
                type: 'POST',
                url: 'photos.php',
                data: {
                    album_id: album_id,
                    photos: ''
                },
                success: function (res) {
                    var srcObj = JSON.parse(res);
                    $(this).lightGallery({
                        dynamic: true,
                        dynamicEl: srcObj
                    })
                }
            });

        })
    });
</script>

</body>
</html>