<?php
    session_start();
    require_once "vendor/autoload.php";
    require_once "functions.php";

    use LinkedIn\Client;
    use LinkedIn\AccessToken;

    //Set client
    $client = new LinkedIn\Client('86173x3h95a7ss','NyNFYhmVr4xNdxd4');
    $client->setRedirectUrl(
        (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]".'/Social-Media-Post-Scheduler/linkedin-callback.php'
    );

    //Get access token
    $accessToken = $client->getAccessToken($_GET['code']);
    $accessData = json_decode(json_encode($accessToken),true);

    //Save access token
    $_SESSION['li_access_token'] = $accessData['token'];
    $_SESSION['li_access_token_expiresAt'] = $accessData['expiresAt'];

    //Redirect
    if(isset($_SESSION['li_access_token']) || $_SESSION['li_access_token'] != '') {

        $values = [
            'li_access_token' => $accessData['token'],
            'li_access_token_expiresAt' => $accessData['expiresAt']
        ];
        $user = [
            'id' => getUserID()
        ];
        sqlUpdate($values,'users',$user);
        
        headerLocation('index.php');
    }
?>
