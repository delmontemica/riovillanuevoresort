<?php
require_once '../backend.php';
ob_start();

if (isset($_GET['from']) && isset($_GET['to'])) {
  $dates = getDatesFromRange($_GET['from'], $_GET['to']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $title . ' Reports'; ?></title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/style.css">
  <style>
    th{
      text-align: center;
      vertical-align: middle !important;
    }
  </style>
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
    <div style="margin-top: 200px">
      <h1 style="font-size:25px;text-align:center;font-family:Helvetica; text-transform:uppercase">
        <div><b><?php echo $title; ?> Reports</b></div>
<?php if (isset($dates)): ?>
        <small>From: <?php echo dateFormat($_GET['from'], 'M d, Y'); ?> To: <?php echo dateFormat($_GET['to'], 'M d, Y'); ?></small>
<?php endif;?>
      </h1>
      <br>
      <table class="table table-bordered table-striped">
<?php if ($_GET['type'] == 'reservation'): ?>
        <thead>
          <tr>
            <th>Reservation ID</th>
            <th>Email Address</th>
            <th>Room Numbers</th>
            <th>No. of Guests</th>
            <th>Expected Check In</th>
            <th>Expected Check Out</th>
            <th>Actual Check In</th>
            <th>Actual Check Out</th>
            <th>Amount Paid</th>
            <th>Total Amount</th>
          </tr>
        </thead>
        <tbody>
<?php
$result = $db->query('
  SELECT
  reservation.reservationID,
  emailAddress,
  adults, children, toddlers,
  reservation.checkIn as expectedCheckIn,
  reservation.checkOut as expectedCheckOut,
  reservation_check.checkIn as actualCheckIn,
  reservation_check.checkOut as actualCheckOut,
  dateCancelled
  FROM reservation
  JOIN reservation_check
  ON reservation.reservationID=reservation_check.reservationID
  LEFT JOIN reservation_cancelled
  ON reservation.reservationID=reservation_cancelled.reservationID
  ORDER BY reservation.reservationID
');
while ($row = $result->fetch_assoc()): ?>
<?php
if ($row['dateCancelled'] || isset($dates) && (!in_array(dateFormat($row['actualCheckIn'], 'Y-m-d'), $dates) || !in_array(dateFormat($row['actualCheckOut'], 'Y-m-d'), $dates))) {
  continue;
}
?>
          <tr>
            <td><?php echo $row['reservationID']; ?></td>
            <td><?php echo $row['emailAddress']; ?></td>
            <td><?php echo join(', ', getAllRooms($row['reservationID'])); ?></td>
            <td>
              Adults: <?php echo $row['adults']; ?><br>
              Children: <?php echo $row['children']; ?><br>
              Toddlers: <?php echo $row['toddlers']; ?><br>
            </td>
            <td><?php echo dateFormat($row['expectedCheckIn'], 'M d, Y'); ?></td>
            <td><?php echo dateFormat($row['expectedCheckOut'], 'M d, Y'); ?></td>
            <td><?php echo dateFormat($row['actualCheckIn'], 'M d, Y h:i:s A'); ?></td>
            <td><?php echo dateFormat($row['actualCheckOut'], 'M d, Y h:i:s A'); ?></td>
            <td><?php echo pesoFormat(getAmountPaid($row['reservationID']), 'Php'); ?></td>
            <td><?php echo pesoFormat(getTotalAmount($row['reservationID']), 'Php'); ?></td>
          </tr>
<?php endwhile;?>
        </tbody>
<?php elseif ($_GET['type'] == 'cancelled'): ?>
        <thead>
          <tr>
            <th>Reservation ID</th>
            <th>Email Address</th>
            <th>Expected Check In</th>
            <th>Expected Check Out</th>
            <th>Date Cancelled</th>
          </tr>
        </thead>
        <tbody>
<?php
$result = $db->query('
  SELECT * FROM reservation
  JOIN reservation_cancelled
  ON reservation.reservationID=reservation_cancelled.reservationID
  ORDER BY reservation.reservationID
');
while ($row = $result->fetch_assoc()): ?>
<?php
if (isset($dates) && (!in_array(dateFormat($row['checkIn'], 'Y-m-d'), $dates) || !in_array(dateFormat($row['checkOut'], 'Y-m-d'), $dates))) {
  continue;
}
?>
          <tr>
            <td><?php echo $row['reservationID']; ?></td>
            <td><?php echo $row['emailAddress']; ?></td>
            <td><?php echo dateFormat($row['checkIn'], 'M d, Y'); ?></td>
            <td><?php echo dateFormat($row['checkOut'], 'M d, Y'); ?></td>
            <td><?php echo dateFormat($row['dateCancelled'], 'M d, Y'); ?></td>
          </tr>
<?php endwhile;?>
        </tbody>
<?php elseif ($_GET['type'] == 'guestranking'): ?>
        <thead>
          <tr>
            <th>Email Address</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Reservation Count</th>
          </tr>
        </thead>
        <tbody>
<?php
$result = $db->query('
  SELECT
  guest.emailAddress,
  firstName,
  lastName,
  COUNT(*) as reservationCount,
  reservation_check.checkIn,
  reservation_check.checkOut
  FROM guest
  JOIN reservation
  ON guest.emailAddress=reservation.emailAddress
  JOIN reservation_check
  ON reservation.reservationID=reservation_check.reservationID
  GROUP BY guest.emailAddress
  ORDER BY reservationCount DESC
');
while ($row = $result->fetch_assoc()): ?>
<?php
if (isset($dates) && (!in_array(dateFormat($row['checkIn'], 'Y-m-d'), $dates) || !in_array(dateFormat($row['checkOut'], 'Y-m-d'), $dates))) {
  continue;
}
?>
          <tr>
            <td><?php echo $row['emailAddress']; ?></td>
            <td><?php echo $row['firstName']; ?></td>
            <td><?php echo $row['lastName']; ?></td>
            <td><?php echo $row['reservationCount']; ?></td>
          </tr>
<?php endwhile;?>
        </tbody>
<?php elseif ($_GET['type'] == 'roomranking'): ?>
        <thead>
          <tr>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Used Count</th>
          </tr>
        </thead>
        <tbody>
<?php
$result = $db->query('
  SELECT
  room.roomID,
  name,
  COUNT(*) as usedCount,
  checkIn, checkOut
  FROM room
  JOIN room_types
  ON room.roomTypeID=room_types.roomTypeID
  JOIN reservation_room
  ON reservation_room.roomID=room.roomID
  JOIN reservation
  ON reservation_room.reservationID=reservation.reservationID
  GROUP BY room.roomID
  ORDER BY usedCount DESC
');
while ($row = $result->fetch_assoc()): ?>
<?php
if (isset($dates) && (!in_array(dateFormat($row['checkIn'], 'Y-m-d'), $dates) || !in_array(dateFormat($row['checkOut'], 'Y-m-d'), $dates))) {
  continue;
}
?>
          <tr>
            <td><?php echo $row['roomID']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['usedCount']; ?></td>
          </tr>
<?php endwhile;?>
        </tbody>
<?php elseif ($_GET['type'] == 'sales'): ?>
        <thead>
          <tr>
            <th width="50%">Date</th>
            <th width="30%">Income</th>
            <th width="20%">Percentage from Past Date</th>
          </tr>
        </thead>
        <tbody>
<?php
$total = 0;
array_unshift($dates, date('Y-m-d', strtotime(date($_GET['from'])) - 86400));
for ($i = 1; $i < count($dates); $i++):
?>
<?php
$previousIncome = getIncomeFromDate($dates[$i - 1]);
$currentIncome  = getIncomeFromDate($dates[$i]);
$percentage     = $previousIncome > 0 ? number_format(($currentIncome - $previousIncome) / $previousIncome * 100, 2, '.', '') : false;
$total += $currentIncome;
?>
          <tr>
            <td><?php echo dateFormat($dates[$i], 'M d, Y'); ?></td>
            <td align="right"><?php echo pesoFormat($currentIncome, 'Php'); ?></td>
            <td align="right" style="color:<?php echo $percentage > 0 ? 'green' : ($percentage < 0 ? 'red' : 'unset'); ?>"><?php echo $percentage == false ? 'N/A' : $percentage . '%'; ?></td>
          </tr>
<?php endfor;?>
          <tr>
            <td colspan="2" align="right"> Total:</td>
            <td align="right"><?php echo pesoFormat($total, 'Php'); ?></td>
          </tr>
        </tbody>
<?php endif;?>
      </table>
    </div>
  </div>
</body>
</html>
<?php
return ob_get_clean();
?>
