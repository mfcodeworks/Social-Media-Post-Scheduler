<?php 
//Display login/logout link if user logged out/in
function LoginLink()
{
    //If logged in display username and logout option
    if(isset($_SESSION['username'])) {
        $user = $_SESSION['username'];
        echo "<a class='nav-link' href='javascript:void(0)' id='logoutLink'>$user, Logout</a>";
    }
    //If not logged in display login option
    else echo '<a class="nav-link" href="login.php">Login</a>';
};
//Load header info
function loadHead()
{
    echo '<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Social Media Post Scheduler</title>
        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet">
        <link href="css/font-awesome.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="css/shop-homepage.css" rel="stylesheet">
        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="functions.js"></script>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      </head>';
};
//Load navigation bar info
function loadNav()
{
    echo "<body>
    <!-- Navigation -->
    <nav class='navbar navbar-expand-lg navbar-dark bg-dark fixed-top'>
      <div class='container'>
        <a class='navbar-brand' href='index.php'>Social Media Post Scheduler</a>
        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarResponsive' aria-controls='navbarResponsive' aria-expanded='false' aria-label='Toggle navigation'>
          <span class='navbar-toggler-icon'></span>
        </button>
        <div class='collapse navbar-collapse' id='navbarResponsive'>
            <ul class='navbar-nav ml-auto'>
            <li class='nav-item' name='index.php'>
              <a class='nav-link' href='index.php'>New Post</a>
            </li>
            <li class='nav-item' name='link-social.php'>
                <a class='nav-link' href='link-social.php'>Link Social Media Profiles</a>
            </li>
            <li class='nav-item' name='login.php'>";
                LoginLink();
        echo "</li>";
        if(!isset($_SESSION['username']))
        echo "<li class='nav-item' name='admin.php'>
                <a class='nav-link' href='admin.php'>Sign Up</a>
            </li>";
        echo "</ul>
        </div>
      </div>
    </nav>";
};
//Begin page main content
function beginContent()
{
    echo "<!-- Page Content -->
            <div class='container'>
                <div class='row'>";
};
//load login page login.php
function loadLogin()
{
    echo "<div class='col-lg-12'>
    <form action='javascript:void(0)' method='post' style='margin: auto; text-align: center;padding-bottom:9.7em;' id='loginForm'>
        <h1 class='my-4' style='margin: auto; width: 50%; text-align: center;'>Login</h1>
        <div class='form-group' style='list-group-item'>
          <label for='username'><i class='fa fa-user'></i>&nbsp;Username</label>
          <input type='text' name='username' class='form-control' id='username'>
        </div>
        <div class='form-group' style='list-group-item'>
          <label for='password'><i class='fa fa-lock'></i>&nbsp;Password</label>
          <input type='password' name='password' class='form-control' id='password'>
        </div>
        <div class='text-center' style='list-group-item'>
          <button type='submit' class='btn btn-primary'><i class='fa fa-sign-in'></i> Log in</button>
        </div>
    </form>
    </div>";
};
//Load footer info
function loadFoot()
{
    echo "</div>
        <!-- /.row -->
        </div>
        <!-- Footer -->
        <footer class='py-5 bg-dark' style='position: relative; right: 0; bottom: 0; left: 0; padding: 1rem; text-align: center;'>
            <div class='container'>
                <p class='m-0 text-center text-white'>Copyright &copy; MF Apps &amp; Web 2017</p>
                <p><a href='privacy-policy.php' style='color:black;'>Privacy Policy</a>
            </div>
        <!-- /.container -->
        </footer>
        </body>
    </html>";
};
?>
