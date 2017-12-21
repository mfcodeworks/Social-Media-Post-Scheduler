<?php
    session_start();
    include 'functions.php';
    extract($_POST);

    // Handle new posts
    if($platformFacebook) {
        // Handle facebook posting...
    };
    if($platformTwitter) {
        // Handle twitter posting...
    };

    // Testing...
    echo "Post text: ".$postText;
?>
