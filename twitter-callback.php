<?php
    session_start();
    include 'functions.php';
    require 'vendor/autoload.php';
    use Abraham\TwitterOAuth\TwitterOAuth;

    define('CONSUMER_KEY', 'ISaflfXPdSBSQ6LNHhJQSsc80');
    define('CONSUMER_SECRET', 'Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE');
    define('OAUTH_CALLBACK', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]".'/Social-Media-Post-Scheduler/twitter-callback.php');

    $request_token = [];
    $request_token['oauth_token'] = $_SESSION['oauth_token'];
    $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

    if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] != $_REQUEST['oauth_token']) {
        // Abort! Something is wrong.
        headerLocation("link-social.php?error=couldn't-link-twitter");
    }

    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);

    $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

    $_SESSION['tw_access_token'] = $access_token;

    if(isset($_SESSION['tw_access_token'])) {
        $values = [
            'tw_oauth_token' => $access_token['oauth_token'],
            'tw_oauth_token_secret' => $access_token['oauth_token_secret']
        ];
        $user = [
            'id' => getUserID()
        ];
        sqlUpdate($values,'users',$user);
        headerLocation("index.php");
    }
?>
