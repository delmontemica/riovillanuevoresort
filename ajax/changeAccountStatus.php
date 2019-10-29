<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $password = escape($_POST['password']);

  if (verifyPassword($password)) {
    $name   = escape($_POST['name']);
    $status = $_POST['status'] == 'true' ? 1 : 0;

    echo changeUserStatus($name, $status, $_POST['type'] == 'admin');
  } else {
    echo 'Invalid Password.';
  }
} else {
  echo 'Invalid Token.';
}
?>
