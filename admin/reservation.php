<?php require_once '../backend.php';?>
<div style="overflow:auto;width:100%">
  <button class='btn btn-primary btn-xs pull-right' style="min-width:100px;margin-bottom:10px" onclick="addReservation()">Add</button>
</div>
<div class="table-responsive">
  <table class="dt table table-hover" width="100%" data-sort-by="desc">
    <thead>
      <th width="5%">Reservation ID</th>
      <th width="15%">Email Address</th>
      <th width="15%">Check</th>
      <th width="15%">No. Of Persons</th>
      <th width="10%">Room IDs</th>
      <th width="15%">Amount Paid</th>
      <th width="15%">Balance</th>
      <th width="10%">Action</th>
    </thead>
    <tbody>
<?php
$result = $db->query('
  SELECT
  reservation.reservationID,
  emailAddress,
  reservation.checkIn, reservation.checkOut,
  adults, children, toddlers,
  dateCreated,
  dateCancelled,
  reservation_check.checkOut as initCheckOut
  FROM reservation
  LEFT JOIN reservation_cancelled
  ON reservation.reservationID=reservation_cancelled.reservationID
  LEFT JOIN reservation_check
  ON reservation.reservationID=reservation_check.reservationID
');

while ($row = $result->fetch_assoc()) {
  if ($row['initCheckOut']) {
    continue;
  }

  $roomResult = $db->query("SELECT * FROM reservation_room WHERE reservationID='{$row['reservationID']}'");
  $roomIDs    = [];
  $check      = $db->query("
    SELECT reservation.checkIn, reservation.checkOut, reservation_bank.filename FROM reservation
    LEFT JOIN reservation_bank
    ON reservation.reservationID=reservation_bank.reservationID
    WHERE reservation.reservationID='{$row['reservationID']}'
  ")->fetch_assoc();

  $checkedIn  = (bool) $check['checkIn'];
  $checkedOut = (bool) $check['checkOut'];

  while ($roomRow = $roomResult->fetch_assoc()) {
    $roomIDs[] = $roomRow['roomID'];
  }

  sort($roomIDs);

  $balance = getTotalAmount($row['reservationID'], false) - getAmountPaid($row['reservationID']);
  echo '<tr ' . ($row['dateCancelled'] ? 'style="color:red"' : '') . '>';
  echo "<td>{$row['reservationID']}</td>";
  echo "<td>{$row['emailAddress']}</td>";
  echo '<td>
          In: ' . dateFormat($row['checkIn'], 'M d, Y') . '<br>
          Out: ' . dateFormat($row['checkOut'], 'M d, Y') . '
        </td>';
  echo "<td>
          Adults: {$row['adults']}<br>
          Children: {$row['children']}<br>
          Toddlers: {$row['toddlers']}
        </td>";
  echo '<td>' . join(', ', $roomIDs) . '</td>';
  echo '<td>' . pesoFormat(getAmountPaid($row['reservationID']));
  if (!$row['dateCancelled']) {
    echo "<a style='cursor:pointer;float:right' onclick='addPayment({$row['reservationID']}," . getAmountPaid($row['reservationID']) . ',' . getTotalAmount($row['reservationID'], false) . ")'><i class='fa fa-plus'></i></a>";
  }
  echo '</td>';
  echo '<td>' . pesoFormat($balance) . '</td>';
  echo '<td>';
  if (!$row['dateCancelled']) {
    if ($check['filename']) {
      echo "<button onclick='window.open(\"{$main_url}image/bankimage/?reservationID={$row['reservationID']}\",\"_blank\",\"height=650,width=1000\")' class='btn btn-primary btn-xs btn-block'>Show Image</button>";
    }
    echo "
      <button onclick='editReservation({$row['reservationID']})' class='btn btn-primary btn-xs btn-block btnEditReservation'>Edit</button>
      <button onclick='cancelReservation({$row['reservationID']})' class='btn btn-primary btn-xs btn-block btnCancelReservation'>Cancel</button>
    ";
  }
  echo '</td>';
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
