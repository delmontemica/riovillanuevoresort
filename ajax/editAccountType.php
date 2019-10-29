<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $username    = escape($_POST['username']);
  $accountType = escape($_POST['cmbAccountType']);

  echo editAccountType($username, $accountType);
} else {
  echo 'Invalid Token.';
}
?>
