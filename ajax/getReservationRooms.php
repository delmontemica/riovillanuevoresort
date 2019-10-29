<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $reservationID = escape($_POST['reservationID']);

  $roomTypes = [];

  $result = $db->query("
    SELECT reservationID, name, room.roomID
    FROM reservation_room
    JOIN room
    ON reservation_room.roomID=room.roomID
    JOIN room_types
    ON room.roomTypeID=room_types.roomTypeID
    WHERE reservationID='{$reservationID}'
  ");

  while ($row = $result->fetch_assoc()) {
    $roomTypes[$row['name']][] = $row['roomID'];
  }

  echo json_encode($roomTypes);
}
?>
