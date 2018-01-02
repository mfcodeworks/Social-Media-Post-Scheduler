<?php
use Abraham\TwitterOAuth\TwitterOAuth;
use LinkedIn\AccessToken;
use LinkedIn\Client;

function postToFacebook($accessToken, $postText, $fbPerson, $pictureLocation = NULL) {
    $fbPerson = strtolower($fbPerson);

    $fb = new Facebook\Facebook([
        'app_id' => '325572871258177',
        'app_secret' => '5f3caf15f63c8b3a7ba230e64af770d5',
        'default_graph_version' => 'v2.11',
    ]);

    //Set page ID
    $fbPerson = trim($fbPerson);

    if($fbPerson != "me") {

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get(
                "/$fbPerson?fields=id",
                $accessToken
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            error_log('Graph returned an error: ' . $e->getMessage());
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            error_log('Facebook SDK returned an error: ' . $e->getMessage());
        }
        $graphNode = $response->getGraphNode();
        $fbPerson = $graphNode->getField('id');

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get(
                "/$fbPerson?fields=access_token",
                $accessToken
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            error_log('Graph returned an error: ' . $e->getMessage());
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            error_log('Facebook SDK returned an error: ' . $e->getMessage());
        }
        $graphNode = $response->getGraphNode();
        $accessToken = $graphNode->getField('access_token');

    }
    echo "Facebook post to $fbPerson\n\n";
    //Set message data (always used)
    $data = ['message' => "$postText"];
    $place = 'feed';
    //Add to data for photo if photo exists
    if(isset($pictureLocation) && $pictureLocation != '') {
        $data['source'] = $fb->fileToUpload("$pictureLocation");
        $place = 'photos';
    }
    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->post("/$fbPerson/$place", $data, "$accessToken");
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        error_log('Graph returned an error: ' . $e->getMessage());
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        error_log('Facebook SDK returned an error: ' . $e->getMessage());
    }
    $graphNode = $response->getGraphNode();
    if(isset($graphNode['id']) && $graphNode['id'] != '')
        return true;
    return false;
};
function postToTwitter($oauth_token,$oauth_token_secret, $postText, $picLocation = NULL) {

    if(isset($picLocation) && $picLocation != '') {
        // Upload photo
        $connection = new TwitterOAuth('ISaflfXPdSBSQ6LNHhJQSsc80','Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE',$oauth_token,$oauth_token_secret);
        $media = $connection->upload('media/upload',['media' => "$picLocation"]);
        $parameters = [
            'status' => "$postText",
            'media_ids' => $media->media_id_string
        ];
        $result = $connection->post('statuses/update',$parameters);
        if($result->id != '') {
            return true;
        }
        return false;
    }
    //Post regular status
    else {
        $connection = new TwitterOAuth('ISaflfXPdSBSQ6LNHhJQSsc80','Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE',$oauth_token,$oauth_token_secret);
        $statuses = $connection->post("statuses/update", ["status" => "$postText"]);
        if($statuses->id != '') {
            return true;
        }
        return false;
    }
};
function postToInstagram($igUser,$igPassword,$igPicLocation,$postText) {
    set_time_limit(0);

    /////// CONFIG ///////
    $username = $igUser;
    $password = $igPassword;
    $debug = false;
    $truncatedDebug = false;
    //////////////////////

    //Check for photo
    if(!(isset($igPicLocation)))
        return false;

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
        error_log("INSTAGRAM API\n\nSomething went wrong: ".$e->getMessage()."\n");
    }

    try {
        $ig->timeline->uploadPhoto($photoFilename, ['caption' => $captionText]);
    } catch (\Exception $e) {
        echo "INSTAGRAM API\n\nSomething went wrong: ".$e->getMessage()."\n";
        error_log("INSTAGRAM API\n\nSomething went wrong: ".$e->getMessage()."\n");
    }

    return true;
};
function postToLinkedIn($li_access_token,$li_access_token_expiresAt,$postText,$appUser,$liPicLocation=NULL,$linkedin_business=NULL) {
    if(isset($linkedin_business) && $linkedin_business != 'null' && $linkedin_business != '' && $linkedin_business != ' ')
        $shareTo = "companies/".$linkedin_business."/shares";
    else
        $shareTo = "people/~/shares";

    $accessToken = new LinkedIn\AccessToken($li_access_token, $li_access_token_expiresAt);
    $client = new LinkedIn\Client('86173x3h95a7ss','NyNFYhmVr4xNdxd4');
    $client->setAccessToken($accessToken);

    if(isset($liPicLocation) && $liPicLocation != '') {
        $share = $client->post(
            "$shareTo",
            [
                'comment' => $postText,
                'content' => [
                    'title' => '',
                    'submitted-url' => "https://$_SERVER[HTTP_HOST]" . "/Social-Media-Post-Scheduler/$appUser/" . basename($liPicLocation),
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
    if($share['updateUrl'] != '') return true;
    return false;
};
function postToDB(Array $values) {
    $values['id'] = getMaxId('posts');
    $conn = sqlConnect();
    $sql = "INSERT INTO posts(";
    foreach($values as $k => $d) {
        $sql.= "$k,";
    }
    $sql = trim($sql,",");
    $sql .= ") VALUES(";
    foreach($values as $k => $d) {
        $sql.= "$d,";
    }
    $sql = trim($sql,",");
    $sql .= ");";
    if(mysqli_query($conn,$sql)) return true;
    else {
        error_log("Couldn't save post to DB. #ID: ".$values['id']);
        return false;
    }
};
function schedulePost($id) {

    $post = sqlGet(['*'],'posts',['id'=>$id]);

    $log = "<html><body>";

    if($post['platformFacebook']) {

        $user = sqlGet(['fb_access_token'], 'users', [ 'id' => $post['appUser'] ] );
        if(!postToFacebook($user['fb_access_token'], $post['postText'], $post['fbPerson'], $post['postPhoto']))
            $log .= "Couldn't post #$id on Facebook.</br>";

    }
    if($post['platformTwitter']) {

        $user = sqlGet(['tw_oauth_token','tw_oauth_token_secret'], 'users', [ 'id' => $post['appUser'] ] );
        if(!postToTwitter($user['tw_oauth_token'], $user['tw_oauth_token_secret'], $post['postText'], $post['postPhoto']))
            $log .= "Couldn't post #$id on Twitter.</br>";

    }
    if($post['platformInstagram']) {

        if(!postToInstagram($post['igUser'], $post['igPassword'], $post['postPhoto'], $post['postText']))
            $log .= "Couldn't post #$id on Instagram.</br>";

    }
    if($post['platformLinkedin']) {

        $user = sqlGet(['li_access_token', 'li_access_token_expiresAt'], 'users', [ 'id' => $post['appUser'] ] );
        if(!postToLinkedIn($user['li_access_token'], $user['li_access_token_expiresAt'], $post['postText'], $post['appUser'], $post['postPhoto'], $post['linkedin_business']))
            $log .= "Couldn't post #$id on LinkedIn.</br>";

    }

    $values = [
        'published' => '1',
        'igUser' => 'NULL',
        'igPassword' => 'NULL',
    ];
    $where = [
        'id' => $id
    ];
    if(!sqlUpdate($values,'posts',$where)) {
        $log("Descheduling post #$id failed.</body></html>");
        error_log($log);
        mailMessage($log,"Scheduled Social Posts");
        return false;
    }

    return true;
};
?>
