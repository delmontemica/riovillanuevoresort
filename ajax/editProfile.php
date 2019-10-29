<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  echo editProfile($_SESSION['account']['email'], $_POST);
} else {
  echo 'Invalid Token.';
}
?>
