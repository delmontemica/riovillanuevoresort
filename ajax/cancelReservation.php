<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $id = escape($_POST['reservationID']);
  echo cancelReservation($id);
} else {
  echo 'Invalid Token.';
}
?>
