<?php require_once '../backend.php';?>

<div class="reportField row">
  <div class="col-md-5">
    <label>Type: </label>
    <select class="form-control">
      <option value="reservation">Reservation</option>
      <option value="cancelled">Cancelled</option>
      <option value="roomranking">Room Ranking</option>
      <option value="guestranking">Guest Ranking</option>
      <option value="sales">Sales Report</option>
    </select>
  </div>
  <div class="col-md-5">
    <label>Date: </label>
    <input type="text" class="form-control" daterangepicker>
  </div>
  <div class="col-md-2" style="line-height:87px">
    <button class="btn btn-primary btn-block" onclick="showReport()">Show</button>
  </div>
</div>
<div class="table-responsive">
  <table class="dt table table-hover" width="100%">
    <thead>
      <th width="10%">Reservation ID</th>
      <th width="15%">Email Address</th>
      <th width="15%">Checked In</th>
      <th width="15%">Checked Out</th>
      <th width="5%">Adults</th>
      <th width="5%">Children</th>
      <th width="5%">Toddlers</th>
      <th width="15%">Room IDs</th>
      <th width="15%">Total Amount</th>
    </thead>
    <tbody>
<?php
$result = $db->query('
    SELECT
    reservation.reservationID,
    emailAddress,
    reservation_check.checkIn,
    reservation_check.checkOut,
    adults,
    children,
    toddlers,
    dateCreated
    FROM reservation
    JOIN reservation_check
    ON reservation.reservationID=reservation_check.reservationID
  ');

while ($row = $result->fetch_assoc()) {
  $roomResult = $db->query("SELECT * FROM reservation_room WHERE reservationID='{$row['reservationID']}'");
  $roomIDs    = [];

  while ($roomRow = $roomResult->fetch_assoc()) {
    $roomIDs[] = $roomRow['roomID'];
  }

  sort($roomIDs);

  echo '<tr>';
  echo "<td>{$row['reservationID']}</td>";
  echo "<td>{$row['emailAddress']}</td>";
  echo '<td>' . dateFormat($row['checkIn'], 'M d, Y') . '<br>' . dateFormat($row['checkIn'], 'h:i:s A') . '</td>';
  echo '<td>' . dateFormat($row['checkOut'], 'M d, Y') . '<br>' . dateFormat($row['checkOut'], 'h:i:s A') . '</td>';
  echo "<td>{$row['adults']}</td>";
  echo "<td>{$row['children']}</td>";
  echo "<td>{$row['toddlers']}</td>";
  echo '<td>' . join(', ', $roomIDs) . '</td>';
  echo '<td>' . pesoFormat(getTotalAmount($row['reservationID'])) . '</td>';
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
