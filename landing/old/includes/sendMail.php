<?php
  ini_set( 'display_errors', 1 );
  error_reporting( E_ALL );
  set_error_handler("var_dump");
  $from = "webmaster@acreativa.com";
  $to = $email;
  $subject = "Checking PHP mail";
  $message = "PHP mail works just fine";
  $headers = "From:" . $from;
  mail($to,$subject,$message, $headers);
  echo "The email message was sent.";
?> 