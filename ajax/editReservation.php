<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $reservationID = escape($_POST['reservationID']);
  $checkInDate   = escape($_POST['checkInDate']);
  $checkOutDate  = escape($_POST['checkOutDate']);
  $adults        = escape($_POST['adults']);
  $children      = escape($_POST['children']);
  $toddlers      = escape($_POST['toddlers']);
  $paymentMethod = escape($_POST['paymentMethod']);
  $rooms         = json_decode($_POST['rooms'], true);

  $allRoomIDs = [];

  $db->query("
    UPDATE reservation
    SET
    checkIn='{$checkInDate}',
    checkOut='{$checkOutDate}',
    adults='{$adults}',
    children='{$children}',
    toddlers='{$toddlers}',
    paymentMethod='{$paymentMethod}'
    WHERE reservationID='{$reservationID}'
  ");

  $db->query("UPDATE reservation_expense SET quantity='{$adults}' WHERE reservationID='{$reservationID}' AND name='Entrance Fee (Adults)'");
  $db->query("UPDATE reservation_expense SET quantity='{$children}' WHERE reservationID='{$reservationID}' AND name='Entrance Fee (Children)'");

  $db->query("DELETE FROM reservation_room WHERE reservationID='{$reservationID}'");
  foreach ($rooms as $roomType => $value) {
    $row        = $db->query("SELECT roomTypeID, rate FROM room_types WHERE name='{$roomType}'")->fetch_assoc();
    $roomTypeID = $row['roomTypeID'];
    $roomRate   = $row['rate'];
    $roomIDs    = generateRoomID($roomTypeID, null, [
      'checkIn'  => $checkInDate,
      'checkOut' => $checkOutDate
    ]); // generate room id based on room type

    foreach ($value as $room) {
      if (!in_array($room, $roomIDs)) {
        echo 'Some rooms already booked. Please try again.';
        die();
      }
    }

    $roomIDs = $value;

    foreach ($roomIDs as $roomID) {
      // insert all rooms to reservation_room with reservation id
      $db->query("INSERT INTO reservation_room VALUES('{$reservationID}','{$roomID}', '{$roomRate}')");
    }

    $allRoomIDs = array_merge($allRoomIDs, $roomIDs);
  }
  sendEmailReservation($reservationID);
  sort($allRoomIDs);
  echo json_encode(['id' => $reservationID, 'rooms' => $allRoomIDs]);
} else {
  echo 'Invalid Token';
}
?>
