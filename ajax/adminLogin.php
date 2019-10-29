<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  echo login([
    'username' => $_POST['username'],
    'password' => $_POST['password']
  ], true);
} else {
  echo 'Invalid Token.';
}
?>
