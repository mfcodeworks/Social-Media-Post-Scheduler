<?php
    extract($_POST);
    error_log($error);

    $to = "mfappsandweb@gmail.com";
    $subject = "Social Post Sheduler AJAX Error";
    $from = "webmaster@mfappsandweb.com";
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $message = "<html><body>" .
                "Error Occured.
                </br>
                Error:
                </br>
                $error" .
                "</body></html>";
    if(!mail($to,$subject,$message,$headers)) error_log("Couldn't send error email.");
?>
