<?php
require_once '../backend.php';

$id  = $id ?? escape($_GET['id']);
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
        <b>Billing Statement</b>
      </h1>
      <br>
      <hr />
      <table style="width:100%; ">
        <tr>
          <td style="width:60%;font-family:Times;font-size:20px;font-weight:bold;font-style:italic;">Invoice #</td>
          <td style="width:40%;font-family:Times;font-size:20px;font-weight:bold;font-style:italic;">Date</td>
        </tr>
        <tr>
          <td style="padding-left:20px"><?php echo date('Y') . str_pad($id, 7, '0', STR_PAD_LEFT); ?></td>
          <td style="padding-left:20px"><?php echo date('M d, Y'); ?></td>
        </tr>
      </table>
      <hr />
      <div class="row" style="margin-left:0;margin-right:0">
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
      <hr/>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="text-align:center;width:55%">Item Description</th>
            <th style="text-align:center;width:15%">Price</th>
            <th style="text-align:center;width:15%">Quantity</th>
            <th style="text-align:center;width:15%">Subtotal</th>
          </tr>
        </thead>
        <tbody>
<?php
$result = $db->query("
  SELECT * FROM reservation
  JOIN reservation_room
  ON reservation.reservationID=reservation_room.reservationID
  WHERE reservation.reservationID='{$id}'
");

$rooms         = [];
$expenses      = [];
$noOfNights    = count(getDatesFromRange($row['checkIn'], $row['checkOut'])) - 1;
$expensesPrice = getTotalExpenses($id);
$roomPrices    = getTotalAmount($id, false) / $noOfNights;
$total         = getTotalAmount($id);

while ($row = $result->fetch_assoc()) {
  $room                             = getRoomIDInfo($row['roomID']);
  $rooms[$room['name']]['quantity'] = isset($rooms[$room['name']]['quantity']) ? $rooms[$room['name']]['quantity'] + 1 : 1;
  $rooms[$room['name']]['price']    = $row['roomRate'];
}

ksort($rooms);

$result = $db->query("SELECT * FROM reservation_expense WHERE reservationID='{$id}'");

while ($row = $result->fetch_assoc()) {
  if ($row['quantity'] == 0) {
    continue;
  }
  $expenses[$row['name']]['quantity'] = $row['quantity'];
  $expenses[$row['name']]['price']    = $row['price'];
}

ksort($expenses);

foreach (array_merge($rooms, $expenses) as $name => $value):
?>
          <tr>
            <td><?php echo $name; ?></td>
            <td align="right"><?php echo pesoFormat($value['price'], 'Php'); ?></td>
            <td align="center"><?php echo $value['quantity']; ?></td>
            <td align="right"><?php echo pesoFormat($value['price'] * $value['quantity'], 'Php'); ?></td>
          </tr>
<?php endforeach;?>
        </tbody>
      </table>
      <table style="width:40%;float:right">
        <tr>
          <td align="right" style="padding:10px">Down Payment:</td>
          <td align="right" style="padding:10px"><?php echo pesoFormat(getDownPayment($id), 'Php'); ?></td>
        </tr>
        <tr>
          <td align="right" style="padding:10px">Remaining Payment:</td>
          <td align="right" style="padding:10px"><?php echo pesoFormat(getRemainingPayment($id), 'Php'); ?></td>
        </tr>
        <tr>
          <td align="right" style="padding:10px">Room Prices:</td>
          <td align="right" style="padding:10px"><?php echo pesoFormat($roomPrices, 'Php'); ?> x <?php echo $noOfNights; ?> night/s</td>
        </tr>
        <tr>
          <td align="right" style="padding:10px">Expenses:</td>
          <td align="right" style="padding:10px"><?php echo pesoFormat($expensesPrice, 'Php'); ?></td>
        </tr>
        <tr>
          <td align="right" style="padding:10px">Vatable Amount:</td>
          <td align="right" style="padding:10px"><?php echo pesoFormat($total - ($total / 1.12 * .12), 'Php'); ?></td>
        </tr>
        <tr>
          <td align="right" style="padding:10px">12% VAT:</td>
          <td align="right" style="padding:10px"><?php echo pesoFormat($total / 1.12 * .12, 'Php'); ?></td>
        </tr>
        <tr>
          <td align="right" style="padding:10px">Total:</td>
          <td align="right" style="padding:10px;font-weight:bold;font-size:22px"><?php echo pesoFormat($total, 'Php') ?></td>
        </tr>
        <tr>
          <td align="right" style="padding:10px">Change:</td>
          <td align="right" style="padding:10px;font-weight:bold;font-size:22px"><?php echo pesoFormat(getAmountPaid($id) - $total, 'Php') ?></td>
        </tr>
      </table>
      <div class="row" style="clear:both;margin-top:60px;margin-left:0;margin-right:0">
        <div class="col-md-6" style="text-align:center;font-size:18px">
          Printed By<br><br><br>
          ___________________________
          <div style="margin-top:-30px"><?php echo getAdminInfo()['name']; ?></div>
        </div>
        <div class="col-md-6" style="text-align:center;font-size:18px">
          Approved By<br><br><br>
          ___________________________
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php
return ob_get_clean();
?>
