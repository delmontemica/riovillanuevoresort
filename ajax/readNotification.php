<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  readNotification(escape($_POST['id']));
} else {
  echo 'Invalid Token.';
}
?>
