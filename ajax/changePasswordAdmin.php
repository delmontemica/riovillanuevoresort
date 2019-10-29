<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $username = escape($_POST['username']);
  $oldPass  = escape($_POST['oldPass']);
  $newPass  = password_hash(escape($_POST['newPass']), PASSWORD_DEFAULT);

  $row = $db->query("SELECT * FROM admin WHERE username='{$username}'")->fetch_assoc();

  if ($row && password_verify($oldPass, $row['password'])) {
    $db->query("UPDATE admin SET password='{$newPass}' WHERE username='{$username}'");

    if ($db->affected_rows > 0) {
      createLog('Update password', $username, true);
      echo true;
    }
  } else {
    echo 'Invalid Password!';
  }
} else {
  echo 'Invalid Token.';
}
?>
