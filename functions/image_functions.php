<?php
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
?>
