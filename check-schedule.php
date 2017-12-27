<?php
    require_once __DIR__ . '/vendor/autoload.php';
    require_once 'functions.php';

    //Connect to DB
    $conn = sqlConnect();
    //Check for scheduled posts
    $sql = "SELECT id,postDate,postTimezone FROM posts WHERE published=0;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $post[] = array(
                'id' => $row['id'],
                'date' => $row['postDate'],
                'timezone' => $row['postTimezone']
        );
    };
    mysqli_close($conn);
    //Check is scheduled post is due for posting
    if(isset($post)) {
        foreach($post as $obj => $data) {
            $dateNow = new DateTime(getDateNow($data['timezone']));
            $scheduleDate = new DateTime($data['date']);
            if($scheduleDate <= $dateNow) $scheduledPost[] = $data['id'];
        }
        if(isset($scheduledPost)) {
            $log = "Scheduled post due for posting:\n";
            //For each scheduled post due for posting, post to platforms selected
            foreach($scheduledPost as $p) {
                $log .= "#$p\n";
                if(!schedulePost($p)) error_log("Could not post scheduled post #$p.");
            }
            $log .= "Posts sent!";
        }
    };
    if(isset($log)) mail("mfappsandweb@gmail.com","Scheduled Social Posts",$log,"From: cronjob@scheduler.com");
?>
