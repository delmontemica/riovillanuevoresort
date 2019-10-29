<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $id = escape($_POST['id']);

  $row = $db->query("
    SELECT * FROM reservation WHERE reservationID='{$id}'
  ")->fetch_assoc();

  $rooms = getAllRooms($id);
  sort($rooms);

  echo json_encode([
    $id, $row['emailAddress'],
    'In: ' . dateFormat($row['checkIn'], 'M d, Y') . '<br>Out: ' . dateFormat($row['checkOut'], 'M d, Y'),
    "Adults: {$row['adults']}<br>Children: {$row['children']}<br>Toddlers: {$row['toddlers']}",
    join($rooms, ', '),
    pesoFormat(getAmountPaid($id)) . "
      <a style='cursor:pointer;float:right' onclick='addPayment({$id})'><i class='fa fa-plus'></i></a>
    ",
    pesoFormat(getTotalAmount($id)),
    "<button onclick='editReservation({$id})' class='btn btn-primary btn-xs btn-block btnEditReservation'>Edit</button>
    <button onclick='cancelReservation({$id})' class='btn btn-primary btn-xs btn-block btnCancelReservation'>Cancel</button>"
  ]);
}
?>
