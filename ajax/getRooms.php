<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  echo json_encode(getRoomTypeInfo(null, escape([
    'checkIn'  => dateFormat($_POST['txtCheckInDate'], 'Y-m-d'),
    'checkOut' => dateFormat($_POST['txtCheckOutDate'], 'Y-m-d')
  ])));
} else {
  echo 'Invalid Token.';
}
?>
