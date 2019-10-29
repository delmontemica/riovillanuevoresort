<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $roomID   = escape($_POST['roomID']);
  $roomType = escape($_POST['cmbRoomType'] ?? '');

  if ($_POST['type'] == 'add') {
    echo addRoom($roomID, $roomType);
  } else if ($_POST['type'] == 'edit') {
    echo editRoom($roomID, $roomType);
  } else if ($_POST['type'] == 'delete') {
    echo deleteRoom($roomID);
  }
} else {
  echo 'Invalid Token.';
}
?>
