<?php
@session_start();
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $_SESSION['booking'] = $_POST;
}
?>
