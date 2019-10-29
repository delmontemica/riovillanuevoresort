<?php require_once '../backend.php';?>
<div class="table-responsive">
  <table class="dt table table-hover" width="100%">
    <thead>
      <th width="5%">Reservation ID</th>
      <th width="15%">Email Address</th>
      <th width="15%">Check</th>
      <th width="15%">Initial Check</th>
      <th width="15%">No. Of Persons</th>
      <th width="15%">Room IDs</th>
      <th width="10%">Total Amount</th>
      <th width="10%">Action</th>
    </thead>
    <tbody>
<?php
$result = $db->query('
  SELECT
  reservation.reservationID,
  emailAddress,
  adults, children, toddlers,
  reservation.checkIn, reservation.checkOut,
  reservation_check.checkIn as actualCheckIn,
  reservation_check.checkOut as actualCheckOut
  FROM reservation
  LEFT JOIN reservation_check
  ON reservation.reservationID=reservation_check.reservationID
');

while ($row = $result->fetch_assoc()) {
  $datesFromBooking = getDatesFromRange($row['checkIn'], date('Y-m-d', strtotime($row['checkOut']) - 86400));
  if (!(in_array(date('Y-m-d'), $datesFromBooking) || ($row['actualCheckIn'] != null && $row['actualCheckOut'] == null))) {
    continue;
  }

  $roomResult = $db->query("SELECT * FROM reservation_room WHERE reservationID='{$row['reservationID']}'");

  $roomIDs = [];
  $check   = $db->query("
    SELECT reservation_check.checkIn, reservation_check.checkOut FROM reservation
    JOIN reservation_check
    ON reservation.reservationID=reservation_check.reservationID
    WHERE reservation.reservationID='{$row['reservationID']}'
  ")->fetch_assoc();

  $checkedIn  = (bool) $check['checkIn'];
  $checkedOut = (bool) $check['checkOut'];

  while ($roomRow = $roomResult->fetch_assoc()) {
    $roomIDs[] = $roomRow['roomID'];
  }

  sort($roomIDs);

  echo '<tr>';
  echo "<td>{$row['reservationID']}</td>";
  echo "<td>{$row['emailAddress']}</td>";
  echo '<td>
          In: ' . dateFormat($row['checkIn'], 'M d, Y') . '<br>
          Out: ' . dateFormat($row['checkOut'], 'M d, Y') . '
        </td>';
  echo '<td>
          In: ' . dateFormat($check['checkIn'], 'M d, Y h:i:s A') . '<br>
          Out: ' . dateFormat($check['checkOut'], 'M d, Y h:i:s A') . '
        </td>';
  echo "<td>
          Adults: {$row['adults']}<br>
          Children: {$row['children']}<br>
          Toddlers: {$row['toddlers']}
        </td>";
  echo '<td>' . join(', ', $roomIDs) . '</td>';
  echo '<td>' . pesoFormat(getTotalAmount($row['reservationID'])) . '</td>';
  echo '<td>';
  if (!$checkedOut) {
    echo "<button class='btn btn-primary btn-xs btn-block' onclick='showExpenses({$row['reservationID']})'>Show Expenses</button>";
  }
  echo "<button data-id='{$row['reservationID']}' class='btn btn-primary btn-xs btn-block btnCheckIn'" . (!$checkedIn && !$checkedOut ? '' : ' disabled') . ">Check In</button>
      <button data-id='{$row['reservationID']}' data-balance='" . (getTotalAmount($row['reservationID']) - getAmountPaid($row['reservationID'])) . "' class='btn btn-primary btn-xs btn-block btnCheckOut'" . ($checkedIn && !$checkedOut ? '' : ' disabled') . ">Check Out</button>
      <button onclick='showBill({$row['reservationID']})' class='btn btn-primary btn-xs btn-block'>Show Bill</button>
    </td>
  ";
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
