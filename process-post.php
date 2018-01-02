<?php
    session_start();
    require_once __DIR__ . '/vendor/autoload.php';
    require_once 'functions.php';

    //Extract info
    extract($_POST);

    //Set user
    $appUser = $_SESSION['username'];
    if(!isset($fbPerson) || $fbPerson == "" || $fbPerson == " ") $fbPerson = "me";

    // Set post datetime
    if(!isset($postDate) || $postDate == '') {
        $date = getDateNow($postTimezone);
    }
    else $date = $postDate;

    //Set photo info
    if($_FILES['postPhoto']['name'] != '') {
        echo "Photo found!\n\n";
        $photo = $_FILES['postPhoto'];
        $picLocation = putPhoto($appUser,$photo);
    }

    //Get times to compare
    $compareDate = new DateTime($date);
    $dateNow = new DateTime(getDateNow($postTimezone));

    //Set post values
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
        'igPassword' => '"'.$igPassword.'"'
    );
    foreach($values as $k => $d) {
        $d = trim($d);
        if(!isset($d) || $d == "" || $d == '' || $d == " ") $values[$k] = 'NULL';
    }

    //If post is sheduled for now
    if($compareDate <= $dateNow) {
        // Handle new posts
        echo "Posting Now!\n\n";

        if($platformFacebook)
            if(postToFacebook($_SESSION['fb_access_token'],$postText,$fbPerson,$picLocation)) echo "Facebook Successful\n\n";
            else echo "Facebook Failed\n\n";
        if($platformTwitter)
            if(postToTwitter($_SESSION['tw_access_token']['oauth_token'],$_SESSION['tw_access_token']['oauth_token_secret'],$postText,$picLocation)) echo "Twitter Successful\n\n";
            else echo "Twitter Failed\n\n";
        if($platformInstagram)
            if(postToInstagram($igUser,$igPassword,$picLocation,$postText)) echo "Instagram Successful\n\n";
            else echo "Instagram Failed\n\n";
        if($platformLinkedin)
            if(postToLinkedIn($_SESSION['li_access_token'], $_SESSION['li_access_token_expiresAt'],$postText,$appUser,$picLocation,$_SESSION['linkedin_business'])) echo "LinkedIn Successful\n\n";
            else echo "LinkedIn Failed\n\n";


        $values['published'] = '1';
        $values['igUser'] = 'NULL';
        $values['igPassword'] = 'NULL';

        if(postToDB($values)) echo "Post saved\n\n";
        else echo "Post save failed\n\n";
    }

    else {
        echo "Post scheduled for $date.\n\n";

        $values['published'] = '0';

        if(postToDB($values)) echo "Post saved\n\n";
        else echo "Couldn't schedule post.";
    };
?>
