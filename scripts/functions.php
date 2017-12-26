<?php
/* Created by Musa Fletcher         *
 * Last Edit 21/12/17                *
 * Licensed under Apache 2 license   *
 *                                  */

 /* Generic functions            */
 function headerLocation($location)
{
    header("Location: ".$location);
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
// console.log() for JS
function consoleLog(string $log) {
    printJS("console.log($log);");
};
// Return todays date as yyyy-mm-dd
function getDateNow()
{
    //Get Date
    $date = date('c', time());
    return $date;
};
// Get date in ISO-8601
function getDateISO($date)
{
    $returnDate = date('c',strtotime($date));
    return $date;
};
// Create a new SQL Connection (Don't forget to close in function!)
function sqlConnect()
{
    $server="localhost";
    $user="nygmaros_Musa";
    $pass="sch@@l12";
    $db="nygmaros_social-media-post";
    //Connect to DB
    $conn = mysqli_connect($server, $user, $pass, $db);
    //Set charset for query
    mysqli_set_charset($conn, "utf8");
    //Alert if connection failed
    if (!$conn) die(printError("Database Connection Failed"));
    return $conn;
};

/* Photo upload functions        */
//Check valid image for uploading
function checkUploadImage($name,$tmpName)
{
    //Check count files
    if(!isset($tmpName)) return false;
    else {
        //Upload check var
        $uploadCheck = 1;
        //Current target file
        $baseName = basename($name);
        //Get file type
        $fileType = pathinfo($baseName,PATHINFO_EXTENSION);
        // Check if image file is an actual image or fake image
        $check = getimagesize($tmpName);
        if($check != false) {
            echo "File is an image - " . $check["mime"] . ".\n\n";
            $uploadCheck = 1;
        }
        else {
            echo "File is not an image.";
            $uploadCheck = 0;
        }
        // Allow certain file formats
        if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" ) {
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadCheck = 0;
        }
    }
    return $uploadCheck;
};
//Give file unique name
function getUniqueName($target)
{
    if(isset($target) && $target != '') {
        //Get file type
        $fileType = pathinfo($target,PATHINFO_EXTENSION);
        //Unique file name
        $newFileName = date('Y-m-d-H-i-s') . '_' . uniqid() . '.' . $fileType;
        return $newFileName;
    }
};
function getFileTarget($file)
{
    $targetNewName = getUniqueName($file);
    $target = $_SERVER['DOCUMENT_ROOT'] . "Social-Media-Post-Scheduler/img/" . $targetNewName;
    if(file_exists($target)) $target = getFileTarget($file);
    return $target;
};
//Try to upload/check upload for file
function uploadFile($source,$target)
{
    if (move_uploaded_file($source, $target)) return true;
    else return false;
};
//Convert files to JPEG
function uploadAsJpeg(string $source,string $target,string $filetype) {
    //Set file target
    if($filetype == "png") {
        $target = str_replace(".png",".jpg",$target);
        $photo = imagecreatefrompng($source);
    }
    else if($filetype == "gif") {
        $target = str_replace(".gif",".jpg",$target);
        $photo = imagecreatefromgif($source);
    }
    //Get file resource
    if(imagejpeg($photo,$target,100)) {
        return $target;
    }
    else return null;
};
function checkImageMaxSize($imagename,int $max_width=1080,int $max_height=1080) {
    echo "Check resize\n\n";
    $image = imagecreatefromjpeg($imagename);

	$w = imagesx($image); //current width
	$h = imagesy($image); //current height
	if ((!$w) || (!$h)) { $GLOBALS['errors'][] = 'Image couldn\'t be resized because it wasn\'t a valid image.'; return false; }

	if (($w <= $max_width) && ($h <= $max_height)) { return $image; } //no resizing needed

	//try max width first...
	$ratio = $max_width / $w;
	$new_w = $max_width;
	$new_h = $h * $ratio;

	//if that didn't work
	if ($new_h > $max_height) {
		$ratio = $max_height / $h;
		$new_h = $max_height;
		$new_w = $w * $ratio;
	}

    echo "Resizing image\n\n";

	$new_image = imagecreatetruecolor ($new_w, $new_h);
	imagecopyresampled($new_image,$image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
    if(imagejpeg($new_image, $imagename,100)) echo "File resized!\n\n";
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
};

/* HTML echo functions         */
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
            <li class='nav-item' name='admin.php'>
              <a class='nav-link' href='admin.php'>Add New User to Group</a>
            </li>
            <li class='nav-item' name='login.php'>";
                LoginLink();
        echo "</li>
            <li class='nav-item' name='link-social.php'>
                <a class='nav-link' href='link-social.php'>Link Social Media Profiles</a>
            </li>
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
    echo "<form action='javascript:void(0)' method='post' style='margin: auto; text-align: center;padding-bottom:9.7em;' id='loginForm'>
        <h1 class='my-4' style='margin: auto; width: 50%; text-align: center;'>Login</h1>
        <div class='form-group' style='list-group-item'>
          <label for='username'>Username</label>
          <input type='text' name='username' class='form-control' id='username'>
        </div>
        <div class='form-group' style='list-group-item'>
          <label for='password'>Password</label>
          <input type='password' name='password' class='form-control' id='password'>
        </div>
        <div class='text-center' style='list-group-item'>
          <button type='submit' class='btn btn-primary'><i class='fa fa-sign-in'></i> Log in</button>
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
