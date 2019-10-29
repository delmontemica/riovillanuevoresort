<?php
require_once '../../backend.php';

$id = $_GET['reservationID'] ?? null;

if ($id) {
  $result = $db->query("
    SELECT * FROM reservation
    JOIN reservation_bank
    ON reservation.reservationID=reservation_bank.reservationID
    WHERE reservation.reservationID='{$id}'
  ");
  $row = $result->fetch_assoc();

  if ($result->num_rows > 0 && (isLogged() && getReservationOwner($id) == getUserInfo()['emailAddress'] || hasPrivilege('Front-desk'))) {
    $filename = file_exists($row['filename']) ? $row['filename'] : 'default';
    $imginfo  = getimagesize($filename);
    header("Content-type: {$imginfo['mime']}");
    readfile($filename);
  } else if ($result->num_rows == 0) {
    // header('Content-type: image/gif');
    // echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    echo "Guest don't have a uploaded file.";
  } else {
    echo "You don't have a permission to access this file.";
  }
}
?>
