<?php
@session_start();
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  echo json_encode($_SESSION['booking'] ?? [
    'step' => 0
  ]);
}
?>
