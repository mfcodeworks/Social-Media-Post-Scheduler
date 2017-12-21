<?php
/* Created by Musa Fletcher         *
 * Last Edit 21/12/17                *
 * Licensed under Apache 2 license   *
 *                                  */

 /* Generic functions            */
 function headerLocation($location)
{
    printJS("window.location.replace(\"$location\");");
};
// Print any errors as Javascript alert
function printError($error)
{
    echo "<script type='text/javascript'>alert(\"$error\");</script>";
};
// Print new JavaScript
function printJS($js) {

        echo "<script type='text/javascript'>$js</script>";
};
// Return todays date as yyyy-mm-dd
function getDateNow()
{
    //Get Date
    date_default_timezone_set('Australia/Brisbane');
    $date = date('Y-m-d', time());
    return $date;
};
// Create a new SQL Connection (Don't forget to close in function!)
function sqlConnect()
{
    $server="localhost";
    $user="nygmaros_Musa";
    $pass="sch@@l12";
    $db="nygmaros_restaurant-order";
    //Connect to DB
    $conn = mysqli_connect($server, $user, $pass, $db);
    //Set charset for query
    mysqli_set_charset($conn, "utf8");
    //Alert if connection failed
    if (!$conn) die(printError("Database Connection Failed"));
    return $conn;
};

/* Login Functions             */
//Password hash function thanks to https://pbeblog.wordpress.com/2008/02/12/secure-hashes-in-php-using-salt/
//Hash password and return hash
function hashPass($pass)
{
    //Create password hash
    $salt = sha1(md5($pass));
    $hashPassword = md5($pass.$salt);
    return $hashPassword;
};
// Get Max ID for Specified SQL Table
function getMaxId($table)
{
    //Connect to DB
    $conn = sqlConnect();
    //Set ID 0
    $id=0;
    $sql="SELECT MAX(ID) FROM $table;";
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result)>0) while($row = mysqli_fetch_assoc($result)) $id = $row["MAX(ID)"];
    mysqli_close($conn);
    //Whether or not result was returned, ID +1
    return $id + 1;
};
//Get Name & Password to insert into SQL DB
function makeUser($name,$password)
{
    //Connect to DB
    $conn = sqlConnect();
    //Get new unique ID to give user
    $id = getMaxId('users');
    //One way salted hash for password
    $password = hashPass($password);
    //Insert post data into DB
    $sql="INSERT INTO users VALUES($id,\"$name\",\"$password\");";
    if(!mysqli_query($conn,$sql)) printError("Database Error. Couldn't make new user.");
    mysqli_close($conn);
};
//Check if user/password combination exist
function checkUser($pass,$user)
{
    //Connect to DB
    $conn = sqlConnect();
    //Get password hash
    $pass = hashPass($pass);
    //Select count of columns that equal data to be checked
    $sql="SELECT count(id) FROM users WHERE password = \"$pass\" AND name = \"$user\";";
    $result = mysqli_query($conn,$sql);
    //Check if User/Password combo exists
    if(mysqli_num_rows($result)>0)
    {
        while($row = mysqli_fetch_assoc($result))
        {
            if($row["count(id)"] > 0) {
                mysqli_close($conn);
                return true;
            }
        }
    }
    mysqli_close($conn);
    return false;
};
//Check if specified data exists in column from given table
function sqlExists($data,$column,$table)
{
    //Connect to DB
    $conn = sqlConnect();
    //Select count of columns that equal data to be checked
    $sql="SELECT count(id) FROM $table WHERE $column = \"$data\";";
    $result = mysqli_query($conn,$sql);
    //Check if column exists with data given
    if(mysqli_num_rows($result)>0)
    {
        while($row = mysqli_fetch_assoc($result))
        {
            if($row["count(id)"] > 0) {
                mysqli_close($conn);
                return true;
            }
        }
    }
    mysqli_close($conn);
    return false;
};
//Save username to users session for future use & proof of login
function setLoggedIn($user)
{
    //Set logged in and save username for session use
    $_SESSION['username']="$user";
    //Get admin level
    $conn = sqlConnect();
    $sql = "SELECT admin_level FROM users WHERE name = \"$user\";";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) $level = $row['admin_level'];
    mysqli_close($conn);
    $_SESSION['adminLevel'] = $level;
};

/* HTML echo functions         */
//Display login/logout link if user logged out/in
function LoginLink()
{
    //If logged in display username and logout option
    if(isset($_SESSION['username'])) {
        $user = $_SESSION['username'];
        echo "<a class='nav-link' href='logout.php'>$user, Logout</a>";
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
            <li class='nav-item' name='admin.php'>
              <a class='nav-link' href='admin.php'>Add New User to Group</a>
            </li>
            <li class='nav-item' name='login.php'>";
                LoginLink();
        echo "</li>
            </ul>
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
    echo "<form action='check-user.php' method='post' style='margin: auto; width: 35%; text-align: center;'>
        <h1 class='my-4' style='margin: auto; width: 50%; text-align: center;'>Login</h1>
        <div class='form-group' style='list-group-item'>
          <label for='username'>Username</label>
          <input type='text' name='username' class='form-control' id='username'>
        </div>
        <div class='form-group' style='list-group-item'>
          <label for='password'>Pin</label>
          <input type='password' name='password' class='form-control' id='password'>
        </div>
        <div class='text-center' style='list-group-item'>
          <button type='submit' class='btn btn-primary'><i class='fa fa-sign-in'></i> Log in</button>
        </div>
    </form>
    <form action='check-user.php' method='post' style='margin: auto; width: 35%; text-align: center;'>
        <h1 class='my-4' style='margin: auto; width: 50%; text-align: center;'>New User</h1>
        <div class='form-group' style='list-group-item'>
          <label for='regUser'>New Username</label>
          <input type='text' name='regUser' class='form-control' id='regUser'>
        </div>
        <div class='form-group' style='list-group-item'>
          <label for='regPassword'>New Pin</label>
          <input type='password' name='regPassword' class='form-control' id='regPassword'>
        </div>
        <div class='text-center' style='list-group-item'>
          <button type='submit' class='btn btn-primary'><i class='fa fa-sign-in'></i> Register</button>
        </div>
    </form>";
};
//Load footer info
function loadFoot()
{
    echo "</div>
        <!-- /.row -->
        </div>
        <!-- Footer -->
        <footer class='py-5 bg-dark' style='position: absolute; right: 0; bottom: 0; left: 0; padding: 1rem; text-align: center;'>
            <div class='container'>
                <p class='m-0 text-center text-white'>Copyright &copy; MF Apps &amp; Web 2017</p>
            </div>
        <!-- /.container -->
        </footer>
        </body>
    </html>";
};
?>
