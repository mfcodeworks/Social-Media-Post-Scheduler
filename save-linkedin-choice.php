<?php
    session_start();
    extract($_POST);
    $_SESSION['linkedin_business'] = $business;
    echo "Success. ".$business." saved.";
?>
