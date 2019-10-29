<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $checkIn  = escape($_POST['checkIn']);
  $checkOut = escape($_POST['checkOut']);

  $roomTypes = [];

  $result = $db->query('SELECT * FROM room_types');

  while ($row = $result->fetch_assoc()) {
    $rooms = generateRoomID($row['roomTypeID'], null, [
      'checkIn'  => $checkIn,
      'checkOut' => $checkOut
    ]);
    sort($rooms);
    $roomTypes[$row['name']] = $rooms;
  }

  echo json_encode($roomTypes);
}
?>
