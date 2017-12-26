<?php
    session_start();
    require_once "vendor/autoload.php";
    require_once "scripts/functions.php";

    use LinkedIn\AccessToken;
    use LinkedIn\Client;

    $accessToken = new LinkedIn\AccessToken($_SESSION['li_access_token'], $_SESSION['li_access_token_expiresAt']);
    $client = new LinkedIn\Client('86173x3h95a7ss','NyNFYhmVr4xNdxd4');
    $client->setAccessToken($accessToken);
    $profile = $client->get(
        'people/~:(id,email-address,first-name,last-name)',
        ['format' => json]
    );
    print_r($profile);
?>
