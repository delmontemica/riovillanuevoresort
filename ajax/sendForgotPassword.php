<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  if (verifyGCaptcha($_POST['g-recaptcha-response'])) {
    $email       = escape($_POST['emailAddress']);
    $credentials = getUserInfo($email);
    if ($credentials && $token = sendForgotPassword($email)) {
      if ($credentials) {
        echo sendEmail([
          'email'   => $email,
          'subject' => 'Verify Forgot Password',
          'body'    => "
<img src='{$main_url}image/logo2.png' style='text-align:center'/>
<br><br>
Dear {$credentials['firstName']} {$credentials['lastName']},
<br><br>
Please click the button to change your password.
<br><br>
<a
  style='padding: 6px 15px;font-size: 20px;background-color: #1abc9c;color:white;cursor:pointer;border:none;text-decoration:none'
  href='{$main_url}forgotpassword.php?email={$email}&token={$token}'
>
VERIFY
</a>
"
        ]);
      }
    } else {
      echo 'Email does not exists.';
    }
  } else {
    echo 'Invalid Captcha!';
  }
} else {
  echo 'Invalid Token.';
}
?>
