<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $roomTypeID = escape($_POST['roomTypeID']);
  echo json_encode($db->query("SELECT * FROM room_types WHERE roomTypeID ='{$roomTypeID}'")->fetch_assoc());
} else {
  echo 'Invalid Token.';
}
?>
