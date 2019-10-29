<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  if (isLogged()) {
    echo json_encode(getUserInfo($_POST['email'] ?? null));
  }
} else {
  echo 'Invalid Token.';
}
?>
