<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $roomID = escape($_POST['roomID']);

  echo json_encode(getRoomIDInfo($roomID));
} else {
  echo 'Invalid Token.';
}
?>
