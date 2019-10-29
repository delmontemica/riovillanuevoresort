<?php require_once '../../backend.php';?>
<h6><a href="#/settings">← Back</a></h6>
<button class='btn btn-primary btn-xs btnAddRoomID pull-right' style="min-width:100px;margin-bottom:10px" data-toggle="modal" data-target="#addRoomIDModal">Add</button>
<div class="table-responsive">
  <table class="dt table table-hover" width="100%">
    <thead>
      <th>Room ID</th>
      <th>Room Type</th>
      <th width="10%">Action</th>
    </thead>
    <tbody>
<?php
$result = $db->query('
  SELECT * FROM room
  JOIN room_types
  ON room.roomTypeID=room_types.roomTypeID
');

while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['roomID']}</td>";
  echo "<td>{$row['name']}</td>";
  echo "
    <td>
      <button data-id='{$row['roomID']}' class='btn btn-primary btn-xs btn-block btnEditRoomID'>Edit</button>
      <button data-id='{$row['roomID']}' class='btn btn-primary btn-xs btn-block btnDeleteRoomID'>Delete</button>
    </td>
  ";
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
