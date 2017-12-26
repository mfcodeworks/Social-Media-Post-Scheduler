<?php
    session_start();
    require_once __DIR__ . '/vendor/autoload.php';
    require_once 'scripts/functions.php';
    use Abraham\TwitterOAuth\TwitterOAuth;
    use LinkedIn\AccessToken;
    use LinkedIn\Client;

    extract($_POST);

    //Set photo info
    if($_FILES['postPhoto']['name'] != '') {
        $photo = $_FILES['postPhoto'];
        $photoSource = $photo['tmp_name'];
        echo "Photo found!\n\n";
    }

    // Set post datetime
    if(isset($postDate) && $postDate != '') {
        $date = getDateISO($postDate);
        $published = false;
    }
    else {
        $date = getDateNow();
        $published = true;
    }
    echo "Date $date\n\n";



    // Handle new posts
    if($platformFacebook) {
        $accessToken = $_SESSION['fb_access_token'];
        // Handle facebook posting...
        $fb = new Facebook\Facebook([
          'app_id' => '325572871258177',
          'app_secret' => '5f3caf15f63c8b3a7ba230e64af770d5',
          'default_graph_version' => 'v2.11',
        ]);

        //Set message data (always used)
        $data = ['message' => "$postText"];
        $place = 'feed';

        //Add to data for photo if photo exists
        if(isset($photo) && $photo != '') {
            $data['source'] = $fb->fileToUpload("$photoSource");
            $place = 'photos';
        }

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->post("/$fbPerson/$place", $data, "$accessToken");
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $graphNode = $response->getGraphNode();

        echo 'Post ID: ' . $graphNode['id'];
    };


    if($platformTwitter) {
        // Handle twitter posting...

        if(isset($photo) && $photo != '') {
            // Upload photo
            $connection = new TwitterOAuth('ISaflfXPdSBSQ6LNHhJQSsc80','Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE',$_SESSION['tw_access_token']['oauth_token'],$_SESSION['tw_access_token']['oauth_token_secret']);
            $media = $connection->upload('media/upload',['media' => "$photoSource"]);
            $parameters = [
                'status' => "$postText",
                'media_ids' => $media->media_id_string
            ];
            $result = $connection->post('statuses/update',$parameters);
            if(isset($result)) {
                echo "Twitter successful!\n\n";
            }
        }
        //Post regular status
        else {
            $connection = new TwitterOAuth('ISaflfXPdSBSQ6LNHhJQSsc80','Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE',$_SESSION['tw_access_token']['oauth_token'],$_SESSION['tw_access_token']['oauth_token_secret']);
            $statuses = $connection->post("statuses/update", ["status" => "$postText"]);
            if(isset($statuses)) {
                echo "Twitter successful!\n\n";
            }
        }
    };


    if($platformInstagram) {
        set_time_limit(0);

        /////// CONFIG ///////
        $username = $igUser;
        $password = $igPassword;
        $debug = false;
        $truncatedDebug = false;
        //////////////////////

        //Try to upload file
        if(isset($photo) && $photo['name']!='') {
            echo "Uploading photo for IG\n\n";
            if(checkUploadImage($photo['name'],$photo['tmp_name'])) {
                echo "\t\tImage checked\n\n";
                $target = getFileTarget($photo['name']);
                echo "\t\tTarget $target\n\n";

                //Check extension
                $fileType = pathinfo(basename($photo['name']),PATHINFO_EXTENSION);
                if($fileType != "jpg" && $fileType != "jpeg") {
                    $igPicLocation = uploadAsJpeg($photoSource,$target,$fileType);
                }
                else {
                    if(uploadFile($photoSource,$target)) $igPicLocation = "img/".basename($target);
                }
            }
        }

        //Check for photo
        if(!(isset($igPicLocation))) {
            echo "Couldn't upload photo locally.";
            exit(0);
        }
        echo "\t\tPicture uploaded at: $igPicLocation\n\n";

        //Check image size
        checkImageMaxSize($igPicLocation);

        /////// MEDIA ////////
        $photoFilename = $igPicLocation;
        $captionText = $postText;
        //////////////////////

        $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

        try {
            $ig->login($username, $password);
        } catch (\Exception $e) {
            echo "INSTAGRAM API\n\nSomething went wrong: ".$e->getMessage()."\n";
        }

        try {
            $ig->timeline->uploadPhoto($photoFilename, ['caption' => $captionText]);
        } catch (\Exception $e) {
            echo "INSTAGRAM API\n\nSomething went wrong: ".$e->getMessage()."\n";
        }

        //Remove uneccessary photo
        unlink($igPicLocation);
    };



    if($platformLinkedin) {
        // Handle linkedin posting...
        if(isset($_SESSION['linkedin_business']) && $_SESSION['linkedin_business'] != 'null') {
            $shareTo = "companies/".$_SESSION['linkedin_business']."/shares";
        }
        else {
            $shareTo = "people/~/shares";
        }

        $accessToken = new LinkedIn\AccessToken($_SESSION['li_access_token'], $_SESSION['li_access_token_expiresAt']);
        $client = new LinkedIn\Client('86173x3h95a7ss','NyNFYhmVr4xNdxd4');
        $client->setAccessToken($accessToken);

        //Try to upload file
        if(isset($photo) && $photo['name']!='') {
            echo "Uploading photo for LinkedIn\n\n";
            if(checkUploadImage($photo['name'],$photo['tmp_name'])) {
                echo "\t\tImage checked\n\n";
                $target = getFileTarget($photo['name']);
                echo "\t\tTarget $target\n\n";

                if(uploadFile($photoSource,$target)) $liPicLocation = "img/".basename($target);
                //Check for photo
                if(!(isset($liPicLocation))) {
                    echo "Couldn't upload photo locally.";
                    exit(0);
                }
                echo "\t\tPicture uploaded at: $liPicLocation\n\n";
            }
        }

        if(isset($liPicLocation) && $liPicLocation != null) {
            $share = $client->post(
                "$shareTo",
                [
                    'comment' => $postText,
                    'content' => [
                        'title' => '',
                        'submitted-url' => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/Social-Media-Post-Scheduler/" . $liPicLocation,
                    ],
                    'visibility' => [
                        'code' => 'anyone'
                    ]
                ]
            );
        }
        else {
            $share = $client->post(
                "$shareTo",
                [
                    'comment' => $postText,
                    'visibility' => [
                        'code' => 'anyone'
                    ]
                ]
            );
        }
    };
?>
