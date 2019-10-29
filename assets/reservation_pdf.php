<?php
require_once '../backend.php';

$id = $id ?? escape($_GET['id']);

$row = $db->query("
  SELECT * FROM reservation
  JOIN guest
  ON reservation.emailAddress = guest.emailAddress
  WHERE reservationID='{$id}'
")->fetch_assoc();

ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/style.css">
</head>
<body>
  <div class="center-block text-center" style="overflow:auto;position:relative;">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <div class="col-md-3">
        <img src="image/orig-logo.jpg" alt="logo" style="height: 200px"/>
      </div>
      <div class="col-md-9 pull-right" style="padding:30px 50px 0 0">
        <div style="font-size:40px;font-style:italic;font-family:Times New Roman;font-weight:bold;">Rio Villa Nuevo</div>
        <div style="font-size:15px;text-transform:uppercase;letter-spacing:3px;margin-top: -10px;font-weight:bold;">Mineral Water Resort</div>
        <div>
          Tambo M. Kulit, Indang, Cavite<br />
          (046) 417 1234 / (+63) 917 123 4567<br />
          admin@riovillanuevoresort.com
        </div>
      </div>
    </div>
    <div class="col-md-3"></div>
    <div style="margin-top: 200px;text-align: left;">
      <h1 style="font-size:25px;text-align:center;font-family:Helvetica; text-transform:uppercase">
        <div><b>Reservation Confirmation</b></div>
        <small>Reservation ID: <?php echo $id; ?></small>
      </h1>
      <br>
      <table style="float:left;width:50%">
        <tr>
          <td style="font-weight:bold;width:30%">Guest Name:</td>
          <td><?php echo $row['firstName'] . ' ' . $row['lastName']; ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;width:30%">Contact Number:</td>
          <td><?php echo $row['contactNumber']; ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;width:30%">Check-in Date:</td>
          <td><?php echo dateFormat($row['checkIn'], 'M d, Y'); ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;width:30%">Check-out Date:</td>
          <td><?php echo dateFormat($row['checkOut'], 'M d, Y'); ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;width:30%">Number of Nights:</td>
          <td><?php echo $noOfNights = count(getDatesFromRange($row['checkIn'], $row['checkOut'])) - 1; ?> </td>
        </tr>
      </table>
      <table style="float:left;width:50%">
        <tr>
          <td style="font-weight:bold;">Payment Method:</td>
          <td style="width:30%"><?php echo $row['paymentMethod']; ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;width:30%" colspan="2">Number of Guests:</td>
        </tr>
        <tr>
          <td style="padding-left:20px" colspan="2">
            Adults: <?php echo $row['adults']; ?> <br>
            Children: <?php echo $row['children']; ?> <br>
            Toddlers: <?php echo $row['toddlers']; ?>
          </td>
        </tr>
      </table>
    </div>
    <div style="margin-top:150px;">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="text-align:center">Rooms</th>
            <th style="text-align:center">Room Number</th>
          </tr>
        </thead>
        <tbody>
<?php
$rooms  = [];
$result = $db->query("
  SELECT * FROM room_types
  JOIN room
  ON room_types.roomTypeID = room.roomTypeID
  JOIN reservation_room
  ON room.roomID = reservation_room.roomID
  WHERE reservationID='{$id}'
");

while ($row = $result->fetch_assoc()) {
  $rooms[$row['name']]['rooms'][] = $row['roomID'];
  $rooms[$row['name']]['price']   = $row['roomRate'];
  sort($rooms[$row['name']]['rooms']);
}

ksort($rooms);

foreach ($rooms as $key => $room):
?>
          <tr>
            <td><?php echo $key; ?></td>
            <td align="center"><?php echo join(', ', $room['rooms']); ?></td>
          </tr>
<?php endforeach;?>
        </tbody>
      </table>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th style="text-align:center">Room Type</th>
            <th style="text-align:center">Price</th>
            <th style="text-align:center">Quantity</th>
            <th style="text-align:center">Amount</th>
          </tr>
        </thead>
        <tbody>
<?php
$total = 0;
foreach ($rooms as $key => $room):
?>
<?php $total += $room['price'] * count($room['rooms'])?>
          <tr>
            <td><?php echo $key; ?></td>
            <td align="right"><?php echo pesoFormat($room['price'], 'Php'); ?></td>
            <td align="right"><?php echo count($room['rooms']); ?></td>
            <td align="right"><?php echo pesoFormat($room['price'] * count($room['rooms']), 'Php'); ?></td>
          </tr>
<?php endforeach;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:right;vertical-align: middle;">Total:</td>
            <td align="right">
              <?php echo pesoFormat($total, 'Php'); ?> x <?php echo $noOfNights; ?> night/s
              <div style="font-size:22px;font-weight:bold"><?php echo pesoFormat($total * $noOfNights, 'Php'); ?></div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div style="border:1px solid #000; padding:20px; text-align: left;margin-bottom: 20px">
      <h1 style="font-size:25px;text-align:left;font-family:Helvetica; text-transform:uppercase">
        Reminders
      </h1>
      <ol>
        <li>Please be reminded that the down payment is 50% of the total amount to be paid for your booking. Your remaining balance is due upon arrival at the resort.</li>
        <li>Entrance fee is not yet included in the total amount.</li>
        <li>Failure to pay within 48 hours, your reservation will be cancelled.</li>
        <li>Kindly send your down payment to the bank account as stated below.</li>
        <li>Once you have already deposited the payment, please upload your deposit slip in the website. The upload option can be seen under your account's "Reservation List" in the account drop-down settings. You may either upload a clear picture or a scanned copy of the slip, as long as the details are visible.</li>
        <li>Please take note of the corkage fee as specified in the rates of the resort.</li>
        <li>Entrance fee is to be paid in the resort and is not counted in the sum of payment for the reservation.</li>
        <li>Modification and cancellation of reservation can be made through phone call. Please refer to our contact details for the resort's telephone number.</li>
        <li>If you wish to cancel your reservation, please make sure the cancellation is made 10 days prior to your reservation date. Be reminded that the payment is no longer refundable once booking is cancelled.</li>
      </ol>
    </div>
    <div style="border:1px solid #000; padding:20px; text-align: left; width:30%; float:right">
      Bank Name: Banco de Oro (BDO)<br>
      Account Number: XXXXXXXXXX<br>
      Amount to be paid: <span style="font-weight:bold;font-size:22px"><?php echo pesoFormat($total * $noOfNights / 2, 'Php'); ?></span>
    </div>
  </div>

</body>
</html>
<?php
return ob_get_clean();
?>
