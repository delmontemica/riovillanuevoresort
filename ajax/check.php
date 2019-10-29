<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  if ($_POST['type'] == 'checkIn') {
    if (checkIn($_POST['reservationID'])) {
      echo json_encode([
        'success' => true,
        'time'    => date('M d, Y h:i:s A')
      ]);
    }
  } else if ($_POST['type'] == 'checkOut') {
    if (checkOut($_POST['reservationID'], escape($_POST['amountPaid']))) {
      echo json_encode([
        'success' => true,
        'time'    => date('M d, Y h:i:s A')
      ]);
    }
  }
} else {
  echo 'Invalid Token.';
}
?>
