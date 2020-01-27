<?php
  $to = "jccastaneda82@misena.edu.co";
  $subject = "Registro Kingly";
  $message = "
  <html>
  <head>
  <title>HTML email</title>
  </head>
  <body>
  <table>
  <tr>
  <th>Nombre</th>
  <th>Email</th>
  <th>Celular</th>
  <th>Ciudad</th>
  <th>Mensaje</th>
  </tr>
  <tr>
  <td>"$name"</td>
  <td>"$email"</td>
  <td>"$phone"</td>
  <td>"$city"</td>
  <td>"$message"</td>
  </tr>
  </table>
  </body>
  </html>
  ";
  // Always set content-type when sending HTML email
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  // More headers
  $headers .= 'From: <webmaster@acreativa.com>' . "\r\n";
  mail($to,$subject,$message,$headers);
?> 