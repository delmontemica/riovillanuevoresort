<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {

  $credentials = [
    'emailAddress' => $_POST['email'],
    'password'     => $_POST['password']
  ];

  echo login($credentials);
} else {
  echo 'Invalid Token.';
}
?>
