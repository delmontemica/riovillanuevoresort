<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $roomType = escape($_POST['roomTypeID']);

  echo json_encode(getRoomTypeInfo($roomType));
} else {
  echo 'Invalid Token.';
}
?>
