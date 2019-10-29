<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $reservationID = escape($_POST['reservationID']);

  echo json_encode($db->query("
    SELECT * FROM reservation WHERE reservationID='{$reservationID}'
  ")->fetch_assoc());
}
?>
