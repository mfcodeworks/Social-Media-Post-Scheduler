<?php
    session_start();
    require_once "functions.php";
    extract($_POST);
    extract($_GET);
    if(isset($error) && $error == "username-taken") {
        alert("Username already taken");
    }
    include 'functions.php';
    loadHead();
    loadNav();
    beginContent();
?>
<div class='col-lg-12' style='padding-bottom:35px;'>
<form action='javascript:void(0)' method='post' style='margin: auto; text-align: center;' id='registerUserForm'>
    <h1 class='my-4' style='margin: auto; width: 50%; text-align: center;'>Sign Up</h1>
    <p style='margin: auto; width: 50%; text-align: center;'>
        You will be added and given permission to post to pages if you have access to it on your social media profile.
        </br>
        An administrator will first enable your account to ensure no malicious activity occurs such as spam bots requesting multiple accounts.
    </p>
    </br>
    <div class='form-group' style='list-group-item'>
      <label for='regUser'><i class='fa fa-user'></i>&nbsp;New Username</label>
      <input type='text' name='regUser' class='form-control' id='regUser'>
    </div>
    <div class='form-group' style='list-group-item'>
      <label for='regEmail'><i class='fa fa-envelope'></i>&nbsp;Email</label>
      <input type='text' name='regEmail' class='form-control' id='regEmail'>
    </div>
    <div class='form-group' style='list-group-item'>
      <label for='regPassword'><i class='fa fa-lock'></i>&nbsp;New Password</label>
      <input type='password' name='regPassword' class='form-control' id='regPassword'>
    </div>
    <div class='text-center' style='list-group-item'>
      <button type='submit' class='btn btn-primary'><i class='fa fa-sign-in'></i> Register</button>
    </div>
</form>
</div>
<?php
    loadFoot();
?>
