<?php
    session_start();
    require_once "vendor/autoload.php";
    require_once "functions.php";

    use LinkedIn\AccessToken;
    use LinkedIn\Client;

    extract($_GET);
    loadHead();
    loadNav();
    beginContent();

    if(isset($error)) {
        if($error == "user-not-enabled") {
            echo "<div class='col-lg-12' style='margin-top:30px;margin-bottom:-40px;'>
                    <p style='color:red;' class='text-center'><i class='fa fa-exclamation-circle'></i>&nbsp;User acount not yet enabled, you can link your social accounts while you wait for your account to be activated.</p>
                </div>";
        }
    }
    if(!isset($_SESSION['fb_access_token'])) {
    echo "<div class='col-lg-12' style='padding-top:4em;padding-bottom:4em;'>
            <div class='row'>
                <button class='btn' onclick='window.location.replace(\"facebook-login.php\");' style='background-color:#3B5998'><i class='fa fa-facebook-square'></i>&nbsp;Link to Facebook</button>
            </div>
        </div>";
    }
    else {
        echo "<div class='col-lg-12' style='padding-top:4em;padding-bottom:4em;'>
                <div class='row'>
                    <button class='btn' style='background-color:#3B5998'><i class='fa fa-facebook-square'></i>&nbsp;Facebook Linked <i class='fa fa-check-circle'></i></button>
                </div>
            </div>";
    }

    if(!isset($_SESSION['tw_access_token'])) {
    echo "<div class='col-lg-12' style='padding-top:4em;padding-bottom:4em;'>
            <div class='row'>
                <button class='btn' onclick='window.location.replace(\"twitter-login.php\");' style='background-color:#00aced'><i class='fa fa-twitter'></i>&nbsp;Link to Twitter</button>
            </div>
        </div>";
    }
    else {
        echo "<div class='col-lg-12' style='padding-top:4em;padding-bottom:4em;'>
                <div class='row'>
                    <button class='btn' style='background-color:#00aced'><i class='fa fa-twitter'></i>&nbsp;Twitter Linked <i class='fa fa-check-circle'></i></button>
                </div>
            </div>";
    }

    if(!isset($_SESSION['li_access_token'])) {
    echo "<div class='col-lg-12' style='padding-top:4em;padding-bottom:4em;'>
            <div class='row'>
                <button class='btn' onclick='window.location.replace(\"linkedin-login.php\");' style='background-color:#4875B4'><i class='fa fa-linkedin'></i>&nbsp;Link to LinkedIn</button>
            </div>
        </div>";
    }
    else {
        echo "<div class='col-lg-12' style='padding-top:4em;padding-bottom:4em;'>
                <div class='row'>
                    <button class='btn' style='background-color:#4875B4'><i class='fa fa-linkedin'></i>&nbsp;LinkedIn Linked <i class='fa fa-check-circle'></i></button>
                </div>
            </div>";

        $accessToken = new LinkedIn\AccessToken($_SESSION['li_access_token'], $_SESSION['li_access_token_expiresAt']);
        $client = new LinkedIn\Client('86173x3h95a7ss','NyNFYhmVr4xNdxd4');
        $client->setAccessToken($accessToken);
        try {
            $profile = $client->get(
                'companies',
                [
                    'format' => json,
                    'is-company-admin' => true
                ]
            );
            echo "<div class='col-lg-9'>
                    <label>
                        <i class='fa fa-linkedin'></i>&nbsp;Select which company to post to for LinkedIn
                    </label>
                    <select id='LinkedInBusiness'>
                        <option value='null'>None</option>";
                        foreach($profile as $b) {
                            echo "<option value=".$b['id'].">".$b['name']."</option>";
                        };
                echo "</select>
                </div>
                <div class='col-lg-3'>
                    <button class='btn' onclick='javascript:void(0)' id='linkedinBusinessChoice'>Save Choice</button>
                </div>";
        }
        catch(Exception $e) {
        };
    };
    loadFoot();
?>
