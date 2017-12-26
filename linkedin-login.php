<?php
    session_start();
    require_once "vendor/autoload.php";
    require_once "scripts/functions.php";

    use LinkedIn\Client;
    use LinkedIn\Scope;

    //Set client
    $client = new LinkedIn\Client('86173x3h95a7ss','NyNFYhmVr4xNdxd4');
    //Set redirect URL
    $client->setRedirectUrl(
            (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]".'/Social-Media-Post-Scheduler/linkedin-callback.php'
    );

    //Set permissions
    $scopes = [
        Scope::READ_BASIC_PROFILE,
        Scope::SHARING,
    ];
    //Get login URL
    $loginUrl = $client->getLoginUrl($scopes);

    //Send user to login
    headerLocation($loginUrl);
?>
