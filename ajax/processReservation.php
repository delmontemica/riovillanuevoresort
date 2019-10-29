<?php
/**
 * 1. insert to database the guest details and reservation details
 * 2. get auto increment id using $db->insert_id
 * 3. for each room type generate a room ids based on quantity
 * 4. store the rooms to roomDetails
 * 5. store all roomid in reservation_room with an assigned reservationID
 * 6. convert to json so i can use it in javascript and supply the table
 */

require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  if (isset($_POST['emailAddress'])) {
    if (isset($_POST['register'])) {
      register(
        [
          'emailAddress'  => $_POST['emailAddress'],
          'password'      => '1234',
          'firstName'     => $_POST['firstName'],
          'lastName'      => $_POST['lastName'],
          'contactNumber' => $_POST['contactNumber'],
          'address'       => $_POST['address']
        ], null, true);
      $email = $_POST['emailAddress'];
    } else {
      $email = $_POST['emailAddress'];
    }
  } else {
    $email = $_SESSION['account']['email'];
  }

  $checkInDate   = dateFormat(escape($_POST['checkInDate']), 'Y-m-d');
  $checkOutDate  = dateFormat(escape($_POST['checkOutDate']), 'Y-m-d');
  $adults        = escape($_POST['adults']);
  $children      = escape($_POST['children']);
  $toddlers      = escape($_POST['toddlers']);
  $paymentMethod = escape($_POST['paymentMethod']);
  $rooms         = gettype($_POST['rooms']) == 'string' ? json_decode($_POST['rooms']) : $_POST['rooms'];

  $db->query("
    INSERT INTO reservation VALUES(
      null,
      '{$email}',
      '{$checkInDate}',
      '{$checkOutDate}',
      '{$adults}',
      '{$children}',
      '{$toddlers}',
      '{$paymentMethod}',
      NOW()
    )
  ");

  $reservationID = $db->insert_id;
  $roomDetails   = [];

  if ($db->affected_rows > 0) {
    // add expenses entrance fee

    $db->query("
      INSERT INTO reservation_expense
      VALUES
      ('{$reservationID}', null, 'Entrance Fee (Adults)', '{$adults}', '150'),
      ('{$reservationID}', null, 'Entrance Fee (Children)', '{$children}', '130'),
      ('{$reservationID}', null, 'Entrance Fee (Senior Citizen)', '0', '120'),
      ('{$reservationID}', null, 'Discounted Coupon (20%)', '0', '120')
    ");

    $content = '';

    foreach ($rooms as $roomType => $value) {
      $row        = $db->query("SELECT roomTypeID, rate FROM room_types WHERE name='{$roomType}'")->fetch_assoc();
      $roomTypeID = $row['roomTypeID'];
      $roomRate   = $row['rate'];

      $roomIDs = generateRoomID($roomTypeID, is_array($value) ? null : $value, [
        'checkIn'  => $checkInDate,
        'checkOut' => $checkOutDate
      ]);

      if (is_array($value)) {
        foreach ($value as $room) {
          if (!in_array($room, $roomIDs)) {
            echo 'Some rooms already booked. Please try again.';
            die();
          }
        }
        $roomIDs = $value;
      } else {
        if (count($roomIDs) != $value) {
          $db->query("DELETE FROM reservation WHERE reservationID='{$reservationID}'");
          $db->query("DELETE FROM reservation_room WHERE reservationID='{$reservationID}'");
          echo 'Some rooms already booked. Please try again.';
          die();
        }
      }

      sort($roomIDs);

      $roomDetails[$roomType] = $roomIDs; // store room type with room id e.g. $roomDetails['Standard Room'] = ['1','2']

      foreach ($roomIDs as $roomID) {
        // insert all rooms to reservation_room with reservation id
        $db->query("INSERT INTO reservation_room VALUES('{$reservationID}','{$roomID}', '{$roomRate}')");
      }
    }

    $emailSent = sendEmailReservation($reservationID);

    /**
     * Convert JSON
     *
     * id = $reservationID [created by $db->insert_id]
     * rooms = $roomDetails
     * e.g.
     *   $roomDetails['Standard Room'] => [1,4,5,6]
     *   $roomDetails['Standard Room 2'] => [2,3]
     *  or
     *   $roomDetails = [
     *     "Standard Room" => [1,4,5,6],
     *     "Standard Room 2" => [2,3]
     *   ]
     */

    echo json_encode([
      'id'    => $reservationID,
      'rooms' => $roomDetails,
      'email' => $emailSent
    ]);
  } else {
    echo $db->error;
  }
} else {
  echo 'Invalid Token.';
}
?>
