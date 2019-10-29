<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $reservationID = escape($_POST['reservationID']);
  $id            = escape($_POST['id']);
  $type          = escape($_POST['type']);
  $value         = escape($_POST['value']);

  switch ($type) {
  case 'name':
    $db->query("UPDATE reservation_expense SET name='{$value}' WHERE reservationID='{$reservationID}' AND id='{$id}'");
    break;

  case 'quantity':
    $db->query("UPDATE reservation_expense SET quantity='{$value}' WHERE reservationID='{$reservationID}' AND id='{$id}'");
    break;

  case 'price':
    $db->query("UPDATE reservation_expense SET price='{$value}' WHERE reservationID='{$reservationID}' AND id='{$id}'");
    break;
  }

  echo $db->affected_rows;
} else {
  echo 'Invalid Token.';
}
?>
