<?php
    session_start();
    extract($_POST);
    include 'scripts/functions.php';
    if(isset($_SESSION["username"])) header("Location: index.php");
    if(isset($_GET["error"]) && $_GET["error"] == "login-incorrect") printError("Username or password incorrect or doesn't exist");
    loadHead();
    loadNav();
    beginContent();
    loadLogin();
    loadFoot();
?>
