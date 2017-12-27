<?php
    session_start();
    require_once __DIR__ . '/vendor/autoload.php';
    require_once 'functions.php';

    //Extract info
    extract($_POST);
    $appUser = $_SESSION['username'];

    // Set post datetime
    if(!isset($postDate) || $postDate == '') {
        $date = getDateNow($postTimezone);
    }
    else $date = $postDate;
    echo "Date to post $date\n\n";

    //Set photo info
    if($_FILES['postPhoto']['name'] != '') {
        echo "Photo found!\n\n";
        $photo = $_FILES['postPhoto'];
        $picLocation = putPhoto($appUser,$photo);
    }

    $compareDate = new DateTime($date);
    $dateNow = new DateTime(getDateNow($postTimezone));

    $values = Array(
        'platformFacebook' => $platformFacebook,
        'platformTwitter' => $platformTwitter,
        'platformInstagram' => $platformInstagram,
        'platformLinkedin' => $platformLinkedin,
        'postText' => '"'.$postText.'"',
        'postPhoto' => '"'.$picLocation.'"',
        'postDate' => '"'.$date.'"',
        'postTimezone' => '"'.$postTimezone.'"',
        'fbPerson' => '"'.$fbPerson.'"',
        'linkedin_business' => '"'.$_SESSION['linkedin_business'].'"',
        'appUser' => getUserId(),
        'igUser' => '"'.$igUser.'"',
        'igPassword' => '"'.$igPassword.'"',
        'fb_access_token' => '"'.$_SESSION['fb_access_token'].'"',
        'tw_oauth_token' => '"'.$_SESSION['tw_access_token']['oauth_token'].'"',
        'tw_oauth_token_secret' => '"'.$_SESSION['tw_access_token']['oauth_token_secret'].'"',
        'li_access_token' => '"'.$_SESSION['li_access_token'].'"',
        'li_access_token_expiresAt' => '"'.$_SESSION['li_access_token_expiresAt'].'"'
    );
    foreach($values as $k => $d) {
        if(!isset($d) || $d == " " || $d == "" || $d == '') $values["$k"] = "NULL";
    }

    if($compareDate <= $dateNow) {
        // Handle new posts
        echo "Posting Now!\n\n";
        $values['published'] = true;

        if($platformFacebook)
            if(postToFacebook($_SESSION['fb_access_token'],$postText,$fbPerson,$picLocation)) echo "Facebook Successful\n\n";
        if($platformTwitter)
            if(postToTwitter($_SESSION['tw_access_token']['oauth_token'],$_SESSION['tw_access_token']['oauth_token_secret'],$postText,$picLocation)) echo "Twitter Successful\n\n";
        if($platformInstagram)
            if(postToInstagram($igUser,$igPassword,$picLocation,$postText)) echo "Instagram Successful\n\n";
        if($platformLinkedin)
            if(postToLinkedIn($_SESSION['li_access_token'], $_SESSION['li_access_token_expiresAt'],$postText,$appUser,$picLocation,$_SESSION['linkedin_business'])) echo "LinkedIn Successful\n\n";

        if(postToDB($values)) echo "Post saved\n\n";
    }
    else {
        //If post scheduled for future TODO...
        echo "Post scheduled for $date.\n\n";

        $values['published'] = '0';

        if(postToDB($values)) echo "Post saved\n\n";
        else echo "Couldn't schedule post.";
    };
?>
