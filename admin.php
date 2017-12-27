<?php
    session_start();
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
    <h1 class='my-4' style='margin: auto; width: 50%; text-align: center;'>Add New User</h1>
    <p style='margin: auto; width: 50%; text-align: center;'>User will be added and given permission to post to pages if they have access to it on their social media profile</p>
    </br>
    <div class='form-group' style='list-group-item'>
      <label for='regUser'>New Username</label>
      <input type='text' name='regUser' class='form-control' id='regUser'>
    </div>
    <div class='form-group' style='list-group-item'>
      <label for='regPassword'>New Password</label>
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
