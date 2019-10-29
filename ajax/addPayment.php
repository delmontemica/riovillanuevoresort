<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  echo addPayment($_POST['reservationID'], $_POST['payment']);
} else {
  echo 'Invalid Token.';
}
?>
