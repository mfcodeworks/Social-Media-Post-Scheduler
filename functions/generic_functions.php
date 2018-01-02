<?php
function headerLocation($location)
{
    header("Location: ".$location);
};
//HTML alert
function alert($message)
{
    printJS("alert($message);");
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
function getDateNow($tz = NULL) {
    //Set timezone
    if(isset($tz)) date_default_timezone_set($tz);

    //Get Date
    $date = date('c');
    return $date;
};
// Return formatted date
function getDateOf($date, $tz = NULL) {
    //Get Date
    if(isset($tz)) date_default_timezone_set($tz);
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
function mailMessage($message, $subject = NULL, $to = "mfappsandweb@gmail.com")
{
    $from = "webmaster@mfappsandweb.nygmarose.com";

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=ISO-8859-1' . "\r\n";
    $headers .= 'From: webmaster@mfappsandweb.nygmarose.com'. "\r\n".
                'X-Mailer: PHP/' . phpversion();

    if(mail($to,$subject,$message,$headers)) return true;
    else error_log("Couldn't send Email!\nTo: $to\nSubject: $subject\nMessage: $message");
};
?>
