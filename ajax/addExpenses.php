<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $reservationID = escape($_POST['reservationID']);
  $name          = escape($_POST['name']);
  $quantity      = escape($_POST['quantity']);
  $price         = escape($_POST['price']);

  $db->query("INSERT INTO reservation_expense VALUES('{$reservationID}', null, '{$name}', '{$quantity}', '{$price}')");

  echo json_encode([
    'id'  => $db->insert_id,
    'aff' => $db->affected_rows
  ]);
} else {
  echo 'Invalid Token.';
}
?>
