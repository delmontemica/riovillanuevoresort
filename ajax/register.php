<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  if (!isset($_POST['admin'])) {
    $credentials = [
      'emailAddress'  => $_POST['emailAddress'],
      'password'      => $_POST['password'],
      'firstName'     => $_POST['firstName'],
      'lastName'      => $_POST['lastName'],
      'contactNumber' => $_POST['contactNumber'],
      'address'       => $_POST['address']
    ];
    if (!isEmailExists($credentials['emailAddress'])) {
      if (verifyGCaptcha($_POST['g-recaptcha-response'])) {
        if (sendEmail([
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
"
        ])) {
          echo register($credentials);
        } else {
          echo 'There was an error sending email to this account.';
        }
      } else {
        echo 'Invalid Captcha!';
      }
    } else {
      echo 'User already registered.';
    }
  } else {
    $credentials = [
      'username'    => $_POST['username'],
      'password'    => $_POST['password'],
      'firstName'   => $_POST['firstName'],
      'lastName'    => $_POST['lastName'],
      'accountType' => $_POST['accountType']
    ];
    if (register($credentials, true)) {
      echo true;
    } else {
      echo 'User already registered.';
    }
  }
} else {
  echo 'Invalid Token.';
}
?>
