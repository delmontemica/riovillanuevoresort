<?php
require_once '../backend.php';
supplyHeaders();

@session_start();
if (verifyToken()) {
  echo 'cleared';
  unset($_SESSION['booking']);
  echo $_SESSION['booking'];
}
?>
