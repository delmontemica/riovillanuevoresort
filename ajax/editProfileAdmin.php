<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $username  = escape($_POST['username']);
  $firstName = escape($_POST['firstName']);
  $lastName  = escape($_POST['lastName']);

  $db->query("UPDATE admin SET firstName='{$firstName}', lastName='{$lastName}' WHERE username='{$username}'");

  if ($db->affected_rows > 0) {
    createLog('Update profile', $username, true);
    echo true;
  } else {
    echo 'Nothing Changed!';
  }
} else {
  echo 'Invalid Token.';
}
?>
