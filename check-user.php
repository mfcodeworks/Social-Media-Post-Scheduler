<?php
    session_start();
    extract($_POST);
    include 'scripts/functions.php';
    //If registering user
    if(isset($regUser))
    {
        if(!sqlExists($regUser,'name','users')) {
            makeUser($regUser,$regPassword);
            setLoggedIn($regUser);
            echo "true";
        }
        else echo "false";
    }
    //If logging in
    if(isset($username))
    {
        if(checkUser($password,$username)) {
            setLoggedIn($username);
            echo "true";
        }
        else echo "false";
    }
?>
