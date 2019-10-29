<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $id = escape($_POST['id']);
  echo uploadImageToReservation($id, $_FILES['file']);
} else {
  echo 'Invalid Token.';
}
?>
