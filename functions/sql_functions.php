<?php
define("SERVER","localhost");
define("DB_USER","nygmaros_Musa");
define("DB_PASSWORD","sch@@l12");
define("DB","nygmaros_social-media-post");

// Create a new SQL Connection (Don't forget to close in function!)
function sqlConnect()
{
    //Connect to DB
    $conn = mysqli_connect(SERVER, DB_USER, DB_PASSWORD, DB);
    //Set charset for queries
    mysqli_set_charset($conn, "utf8");
    //Alert if connection failed
    if( !$conn ) printError("Database Connection Failed");
    return $conn;
};
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
//Put data into SQL
function sqlInsert($data = ['' => ''],$table)
{
    $conn = sqlConnect();
    $sql = "INSERT INTO $table(";
    $values = "VALUES(";
    foreach($data as $c => $d) {
        $sql .= "$c,";
        $values = "$d,";
    }
    $sql = trim($sql,',');
    $values = trim($values,',');
    $sql .= ") " . $values . ");";
    if(mysqli_query($conn,$sql)) return true;
    return false;
};
function sqlGet($values = [],$table,$where = ['' => ''])
{
    $conn = sqlConnect();

    if( count($values) > 1 ) {
        $values = implode(", ", $values);
    }
    else $values = $values[0];

    $sql = "SELECT $values FROM $table";

    if(isset($where)) {

        $condition = " WHERE";
        foreach($where as $c => $d) {
            $condition .= " $c = '$d' AND";
        }
        $condition = rtrim($condition,"AND");

        $sql .= $condition;
    }
    $sql = trim($sql);
    $sql .= ";";

    $result = mysqli_query($conn,$sql);
    if($row = mysqli_fetch_assoc($result)) {
        mysqli_close($conn);
        return $row;
    }
    return false;
};
function sqlUpdate($values = [''=>''],$table,$where = [''=>''])
{
    $conn = sqlConnect();
    $sql = "UPDATE $table SET";
    foreach($values as $c => $d) {
        $sql .= " $c = '$d',";
    }
    $sql = trim($sql,",");
    if(isset($where)) {

        $condition = " WHERE";
        foreach($where as $c => $d) {
            $condition .= " $c = '$d' AND";
        }
        $condition = rtrim($condition,"AND");

        $sql .= $condition;
    }
    $sql .= ";";
    if(mysqli_query($conn,$sql)) return true;
    return false;
};
?>
