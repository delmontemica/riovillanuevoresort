<?php
require_once '../backend.php';

$checkIn  = date('Y-m-d');
$checkOut = date('Y-m-d', strtotime(date('Y-m-d')) + 86400);

$reservationsToday = getReservationOnDate(date('Y-m-d'), date('Y-m-d', time() + 86400));

$details = [
  [
    'title'  => 'Available Rooms',
    'icon'   => 'fa-home',
    'value'  => count(generateRoomID(null, null, [
      'checkIn'  => date('Y-m-d'),
      'checkOut' => date('Y-m-d', strtotime(date('Y-m-d')) + 86400)
    ])),
    'bottom' => 'This is the rooms available from now to tomorrow.'
  ],
  [
    'title'  => 'Guest Accounts',
    'icon'   => 'fa-user',
    'value'  => $db->query('SELECT * FROM guest')->num_rows,
    'bottom' => "<a href='#/accounts' class='center-block text-center'>Go to Guest Accounts</a>"
  ],
  [
    'title'  => 'Admin Accounts',
    'icon'   => 'fa-user-secret',
    'value'  => $db->query('SELECT * FROM admin')->num_rows,
    'bottom' => "<a href='#/accounts' class='center-block text-center'>Go to Admin Accounts</a>"
  ],
  [
    'title'  => 'Ongoing',
    'icon'   => 'fa-exclamation',
    'value'  => count($reservationsToday),
    'bottom' => 'This is the ongoing booking today'
  ]
];

foreach ($details as $detail):
?>
<div class="col-md-4">
  <div class="card">
    <div class="card-icon">
      <i class="fa <?php echo $detail['icon'] ?>"></i>
    </div>
    <div class="card-right">
      <div style="color:#ccc"><?php echo $detail['title'] ?></div>
      <div style="color:#777"><?php echo $detail['value'] ?></div>
    </div>
    <div class="card-bottom">
      <hr>
      <div><?php echo $detail['bottom'] ?></div>
    </div>
  </div>
</div>
<?php
endforeach;
?>
<div class="col-md-8">
  <div style="max-height:200px;overflow:auto">
    <table class="table table-striped table-hover">
      <thead>
        <th width="10%">Reservation ID</th>
        <th width="20%">Email Address</th>
        <th width="20%">Check In Date</th>
        <th width="20%">Check Out Date</th>
        <th width="30%">Room IDs</th>
      </thead>
      <tbody>
<?php
if (count($reservationsToday) == 0):
?>
        <tr><td colspan="5" align="center">No on-going reservation.</td></tr>
<?php
else:
?>
<?php
foreach ($reservationsToday as $row):
?>
        <tr onclick="if(confirm('Are you sure do you want to go to check in and out page?')) location.href='#/checkinandout?s=<?php echo $row['reservationID']; ?>'" style="cursor:pointer">
          <td><?php echo $row['reservationID']; ?></td>
          <td><?php echo $row['emailAddress']; ?></td>
          <td><?php echo dateFormat($row['checkIn'], 'M d, Y'); ?></td>
          <td><?php echo dateFormat($row['checkOut'], 'M d, Y'); ?></td>
          <td><?php echo join(', ', getAllRooms($row['reservationID'])); ?></td>
        </tr>
<?php
endforeach;
endif;
?>
      </tbody>
    </table>
  </div>
</div>
