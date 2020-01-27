<?php
  include "config.php";  //include the DB config file
  //Retrieve form data. 
  $name=$_POST['name'];
  $email=$_POST['email'];
  $phone=$_POST['phone'];
  $city=$_POST['city'];
  $message=$_POST['message'];
  $date_at= date("Y-m-d H:i:s");

  //insert it to database and and echo 1 for success 
  $mysqli->query("INSERT INTO Person (name,email,phone,city,message,date_at) VALUES('$name','$email','$phone','$city','$message', '$date_at') ");

  $to      = 'jccastaneda82@misena.edu.co';
  $subject = 'the subject';
  $message = 'hello';
  $headers = 'From: contact@kinglybulldogs.com' . "\r\n" .
      'Reply-To: contact@kinglybulldogs.com' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();

  $success = mail($to, $subject, $message, $headers);
  if($success){
    echo "Salio";
  }else{
    echo "No salio";
  }

  if($mysqli->error){
    echo $mysqli->error;
  }else{
    echo '1';
  }
?>
