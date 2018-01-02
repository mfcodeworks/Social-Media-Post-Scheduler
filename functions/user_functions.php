<?php
//Get Name & Password to insert into SQL DB
function makeUser($name,$password,$email) {
    //Connect to DB
    $conn = sqlConnect();
    //Get new unique ID to give user
    $id = getMaxId('users');
    //One way salted hash for password
    $password = hashPass($password);
    //Insert post data into DB
    $sql="INSERT INTO users(id,name,email,password,enabled) VALUES($id,\"$name\",\"$email\",\"$password\",0);";
    if(!mysqli_query($conn,$sql)) printError("Database Error. Couldn't make new user.");
    else {
        $subject = "Social Post Sheduler Account Created";
        $message = "<html><body>
                    New Account Created.
                    <br>
                    ID #$id, Username $name, Email $email.
                    <br>
                    Enable <a href='https://mfappsandweb.nygmarose.com/Social-Media-Post-Scheduler/enable-account.php?id=$id'>here</a>
                    </body></html>";
        if(!mailMessage($message,$subject)) error_log("Couldn't send new user email. User #$id.");
    }
    mysqli_close($conn);
};
//Get User ID
function getUserID($username = NULL) {
    if(!isset($username)) $username = $_SESSION['username'];
    $conn = sqlConnect();
    $sql = "SELECT id FROM users WHERE name='$username';";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) $id = $row['id'];
        return $id;
    }
    return false;
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
//Check user account is active
function userIsEnabled($user) {
     //Connect to DB
     $conn = sqlConnect();
     //Check enabled status
     $enabled = 0;
     $sql = "SELECT enabled FROM users WHERE name = '$user';";
     $result = mysqli_query($conn,$sql);
     while($row = mysqli_fetch_assoc($result)) $enabled = $row['enabled'];
     return $enabled;
};
//Save username to users session for future use & proof of login
function setLoggedIn($user,$enabled)
{
    //Set logged in and save username for session use
    $_SESSION['username']="$user";
    $_SESSION['enabled']=$enabled;
};
?>
