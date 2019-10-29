<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $oldPassword = escape($_POST['oldPassword']);
  $newPassword = escape($_POST['newPassword']);

  echo changePassword(null, $oldPassword, $newPassword);
} else {
  echo 'Invalid Token.';
}
?>
