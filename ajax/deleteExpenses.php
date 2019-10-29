<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  $id = escape($_POST['id']);

  $db->query("DELETE FROM reservation_expense WHERE id='{$id}'");

  echo $db->affected_rows;
} else {
  echo 'Invalid Token.';
}
?>
