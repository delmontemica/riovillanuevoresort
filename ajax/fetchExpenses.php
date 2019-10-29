<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $id = escape($_POST['id']);

  $infos = [];

  $result = $db->query("SELECT * FROM reservation_expense WHERE reservationID='{$id}'");

  while ($row = $result->fetch_assoc()) {
    $infos[] = [
      'id'       => $row['id'],
      'name'     => $row['name'],
      'quantity' => $row['quantity'],
      'price'    => $row['price']
    ];
  }

  echo json_encode($infos);
} else {
  echo 'Invalid Token.';
}
?>
