<?php
    session_start();
    extract($_POST);
    include 'functions.php';

    //If registering user
    if(isset($regUser))
    {
        if(!sqlExists($regUser,'name','users')) {
            makeUser($regUser,$regPassword,$regEmail);
            setLoggedIn($regUser,0);
            echo "true";
        }
        else echo "false";
    }

    //If logging in
    if(isset($username))
    {
        if(checkUser($password,$username)) {
            $enabled = userIsEnabled($username);
            setLoggedIn($username,$enabled);
            echo "true";
        }
        else echo "false";
    }
?>
