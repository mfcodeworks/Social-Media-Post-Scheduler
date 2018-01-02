<?php
    session_start();
    require_once 'vendor/autoload.php';
    require_once 'functions.php';
    use Abraham\TwitterOAuth\TwitterOAuth;

    define('CONSUMER_KEY', 'ISaflfXPdSBSQ6LNHhJQSsc80');
    define('CONSUMER_SECRET', 'Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE');
    define('OAUTH_CALLBACK', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]".'/Social-Media-Post-Scheduler/twitter-callback.php');

    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);



    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    $url = $connection->url('oauth/authorize', array('oauth/token' => $request_token['oauth_token']));
    $url = str_replace("%2F","_",$url);
    header('Location: '.$url);
?>
