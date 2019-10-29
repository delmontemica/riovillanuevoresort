<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  echo json_encode(getAdminInfo($_POST['username'] ?? null));
} else {
  echo 'Invalid Token.';
}
?>
