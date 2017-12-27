<?php
/* Created by Musa Fletcher          *
 * Last Edit 21/12/17                *
 * Licensed under Apache 2 license   *
 *                                   */
require_once "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;
use LinkedIn\AccessToken;
use LinkedIn\Client;

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
// Return todays date as yyyy-mm-dd hh:mm:ss
function getDateNow($tz) {
    //Get Date
    date_default_timezone_set($tz);
    $date = date('c');
    return $date;
};
// Return formatted date
function getDateOf($date, $tz) {
    //Get Date
    date_default_timezone_set($tz);
    $date = date('c', $date);
    return $date;
};
// Timezone List Return
function timezone_list() {
    static $timezones = null;

    if ($timezones === null) {
        $timezones = [];
        $offsets = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
    }

    return $timezones;
};
function format_GMT_offset($offset) {
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
};
function format_timezone_name($name) {
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
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
    $target = $_SERVER['DOCUMENT_ROOT'] . "/Social-Media-Post-Scheduler/".$_SESSION['username']."/" . $targetNewName;
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
function uploadAsJpeg( $source, $target, $filetype) {
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
        echo "Target $target\n\n";
        return $target;
    }
    else return null;
};
//Check image size and resize if needed
function checkImageMaxSize($imagename,$max_width=1080,$max_height=1080) {
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
function putPhoto($appUser,$photo) {
    $photoSource = $photo['tmp_name'];

    //Check User dir exists
    if(!is_dir($_SERVER['DOCUMENT_ROOT'] . "/Social-Media-Post-Scheduler/" . $appUser)) {
        mkdir($_SERVER['DOCUMENT_ROOT'] . "/Social-Media-Post-Scheduler/" . $appUser);
    }
    echo "Uploading photo\n\n";

    //Check photo is a photo
    if(checkUploadImage($photo['name'],$photo['tmp_name'])) {

        echo "Image Checked\n\n";

        //Get target dir
        $target = getFileTarget($photo['name']);

        //Check extension
        $fileType = pathinfo(basename($photo['name']),PATHINFO_EXTENSION);

        //Upload as JPEG
        if($fileType != "jpg" && $fileType != "jpeg") {
            $igPicLocation = uploadAsJpeg($photoSource,$target,$fileType);
            if(isset($igPicLocation))
                echo "Uploaded as Jpeg. Saved at $igPicLocation\n\n";
        }
        else {
            if(uploadFile($photoSource,$target)) {
                $igPicLocation = $target;
                echo "Jpeg, uploaded.\n\n";
            }
        }

        $igPicLocation = $_SERVER["DOCUMENT_ROOT"] . "/Social-Media-Post-Scheduler/$appUser/".basename($igPicLocation);
        return $igPicLocation;
    }
};

/* Post Functions              */
function postToFacebook($accessToken, $postText, $fbPerson, $pictureLocation = NULL) {
    $fb = new Facebook\Facebook([
        'app_id' => '325572871258177',
        'app_secret' => '5f3caf15f63c8b3a7ba230e64af770d5',
        'default_graph_version' => 'v2.11',
    ]);
    //Set message data (always used)
    $data = ['message' => "$postText"];
    $place = 'feed';
    //Add to data for photo if photo exists
    if(isset($pictureLocation) && $pictureLocation != '') {
        $data['source'] = $fb->fileToUpload("$pictureLocation");
        $place = 'photos';
    }
    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->post("/$fbPerson/$place", $data, "$accessToken");
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    $graphNode = $response->getGraphNode();
    if(isset($graphNode['id']) && $graphNode['id'] != '')
        return true;
    return false;
};
function postToTwitter($oauth_token,$oauth_token_secret, $postText, $picLocation = NULL) {

    if(isset($picLocation) && $picLocation != '') {
        // Upload photo
        $connection = new TwitterOAuth('ISaflfXPdSBSQ6LNHhJQSsc80','Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE',$oauth_token,$oauth_token_secret);
        $media = $connection->upload('media/upload',['media' => "$picLocation"]);
        $parameters = [
            'status' => "$postText",
            'media_ids' => $media->media_id_string
        ];
        $result = $connection->post('statuses/update',$parameters);
        if($result->id != '') {
            return true;
        }
        return false;
    }
    //Post regular status
    else {
        $connection = new TwitterOAuth('ISaflfXPdSBSQ6LNHhJQSsc80','Ikvl5Pzi9kRJ8Yyyo9GDTjnIrurqkNWvcakuTGpRPYaRHhYGyE',$oauth_token,$oauth_token_secret);
        $statuses = $connection->post("statuses/update", ["status" => "$postText"]);
        if($statuses->id != '') {
            return true;
        }
        return false;
    }
};
function postToInstagram($igUser,$igPassword,$igPicLocation,$postText) {
    set_time_limit(0);

    /////// CONFIG ///////
    $username = $igUser;
    $password = $igPassword;
    $debug = false;
    $truncatedDebug = false;
    //////////////////////

    //Check for photo
    if(!(isset($igPicLocation)))
        return false;

    //Check image size
    checkImageMaxSize($igPicLocation);

    /////// MEDIA ////////
    $photoFilename = $igPicLocation;
    $captionText = $postText;
    //////////////////////

    $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

    try {
        $ig->login($username, $password);
    } catch (\Exception $e) {
        echo "INSTAGRAM API\n\nSomething went wrong: ".$e->getMessage()."\n";
        exit(0);
    }

    try {
        $ig->timeline->uploadPhoto($photoFilename, ['caption' => $captionText]);
    } catch (\Exception $e) {
        echo "INSTAGRAM API\n\nSomething went wrong: ".$e->getMessage()."\n";
        exit(0);
    }

    return true;
};
function postToLinkedIn($li_access_token,$li_access_token_expiresAt,$postText,$appUser,$liPicLocation=NULL,$linkedin_business=NULL) {
    if(isset($linkedin_business) && $linkedin_business != 'null')
        $shareTo = "companies/".$linkedin_business."/shares";
    else
        $shareTo = "people/~/shares";

    $accessToken = new LinkedIn\AccessToken($li_access_token, $li_access_token_expiresAt);
    $client = new LinkedIn\Client('86173x3h95a7ss','NyNFYhmVr4xNdxd4');
    $client->setAccessToken($accessToken);

    if(isset($liPicLocation) && $liPicLocation != '') {
        $share = $client->post(
            "$shareTo",
            [
                'comment' => $postText,
                'content' => [
                    'title' => '',
                    'submitted-url' => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/Social-Media-Post-Scheduler/$appUser/" . basename($liPicLocation),
                ],
                'visibility' => [
                    'code' => 'anyone'
                ]
            ]
        );
    }
    else {
        $share = $client->post(
            "$shareTo",
            [
                'comment' => $postText,
                'visibility' => [
                    'code' => 'anyone'
                ]
            ]
        );
    }
    if($share['updateUrl'] != '') return true;
    return false;
};
function postToDB(Array $values) {
    $values['id'] = getMaxId('posts');
    $conn = sqlConnect();
    $sql = "INSERT INTO posts(";
    foreach($values as $k => $d) {
        $sql.= "$k,";
    }
    $sql = trim($sql,",");
    $sql .= ") VALUES(";
    foreach($values as $k => $d) {
        $sql.= "$d,";
    }
    $sql = trim($sql,",");
    $sql .= ");";
    if(mysqli_query($conn,$sql)) return true;
    else return false;
}
function schedulePost($id) {
    $conn = sqlConnect();
    $sql = "SELECT * FROM posts WHERE id='$id';";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $platformFacebook = $row['platformFacebook'];
        $platformTwitter = $row['platformTwitter'];
        $platformInstagram = $row['platformInstagram'];
        $platformLinkedin = $row['platformLinkedin'];
        $postText = $row['postText'];
        $postPhoto = $row['postPhoto'];
        $fbPerson = $row['fbPerson'];
        $linkedin_business = $row['linkedin_business'];
        $appUser = $row['appUser'];
        $igUser = $row['igUser'];
        $igPassword = $row['igPassword'];
        $fb_access_token = $row['fb_access_token'];
        $tw_oauth_token = $row['tw_oauth_token'];
        $tw_oauth_token_secret = $row['tw_oauth_token_secret'];
        $li_access_token = $row['li_access_token'];
        $li_access_token_expiresAt = $row['li_access_token_expiresAt'];
    }

    $log = "";

    if($platformFacebook) {
        if(!postToFacebook($fb_access_token,$postText,$fbPerson,$postPhoto))
            $log .= "Couldn't post #$id on Facebook.\n";
    }
    if($platformTwitter) {
        if(!postToTwitter($tw_oauth_token,$tw_oauth_token_secret,$postText,$postPhoto))
            $log .= "Couldn't post #$id on Twitter.\n";
    }
    if($platformInstagram) {
        if(!postToInstagram($igUser,$igPassword,$postPhoto,$postText))
            $log .= "Couldn't post #$id on Instagram.\n";
    }
    if($platformLinkedin) {
        if(!postToLinkedIn($li_access_token,$li_access_token_expiresAt,$postText,$appUser,$postPhoto,$linkedin_business))
            $log .= "Couldn't post #$id on LinkedIn.\n";
    }

    $sql = "UPDATE posts SET published='1',igUser=NULL,igPassword=NULL,fb_access_token=NULL,tw_oauth_token=NULL,tw_oauth_token_secret=NULL,li_access_token=NULL,li_access_token_expiresAt=NULL WHERE id='$id';";

    if(!mysqli_query($conn,$sql)) {
        $log("Descheduling post #$id failed.");
        mail("mfappsandweb@gmail.com","Scheduled Social Posts",$log,"From: cronjob@scheduler.com");
        return false;
    }
    mysqli_close($conn);

    if(strlen($log) > 1) mail("mfappsandweb@gmail.com","Scheduled Social Posts",$log,"From: cronjob@scheduler.com");
    return true;
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
//Get User ID
function getUserID() {
    $username = $_SESSION['username'];
    $conn = sqlConnect();
    $sql = "SELECT id FROM users WHERE name='$username';";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) $id = $row['id'];
        return $id;
    }
    return false;
}
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
            </li>";
        if(isset($_SESSION['username']) && $_SESSION['username'] == "Musa") {
            echo "<li class='nav-item' name='admin.php'>
                    <a class='nav-link' href='admin.php'>Add New User to Group</a>
                </li>";
        }

        echo "<li class='nav-item' name='login.php'>";
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
    echo "<div class='col-lg-12'>
    <form action='javascript:void(0)' method='post' style='margin: auto; text-align: center;padding-bottom:9.7em;' id='loginForm'>
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
