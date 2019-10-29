<?php
require_once '../backend.php';

if (isLogged()) {
  $credentials = getUserInfo(escape($_SESSION['account']['email']));
  sendEmail([
    'email'   => $credentials['emailAddress'],
    'subject' => 'Email Address Verification',
    'body'    => "
<img src='{$main_url}image/logo2.png' style='text-align:center'/>
<br><br>
Dear {$credentials['firstName']} {$credentials['lastName']},
<br><br>
Please click the button to verify your email.
<br><br>
<a
  style='padding: 6px 15px;font-size: 20px;background-color: #1abc9c;color:white;cursor:pointer;border:none;text-decoration:none'
  href='{$_SERVER['SERVER_NAME']}{$base_url}ajax/verifyEmail.php?token=" . encrypt($credentials['emailAddress']) . "'
>
VERIFY
</a>
" ]);
}

echo "<script>history.back();alert('Email Verification has been resent.')</script>";
?>