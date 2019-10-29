<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  if (verifyForgotPasswordToken($_POST['email'], $_POST['token'])) {
    useForgotPasswordToken($_POST['email'], $_POST['token']);
    echo changePassword($_POST['email'], null, $_POST['newPassword']);
  } else {
    echo 'Invalid Password Token!';
  }
} else {
  echo 'Invalid Token.';
}
?>
