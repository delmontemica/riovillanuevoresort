<?php require_once '../../backend.php';?>
<h6><a href="#/settings">← Back</a>
<button class='btn btn-primary btn-xs btnAddRoomType pull-right' style="min-width:100px;margin-bottom:10px">Add</button>
</h6>
<div class="table-responsive">
  <table class="dt table table-hover" data-sort="3" width="100%">
    <thead>
      <th>Name</th>
      <th>Image</th>
      <th>Description</th>
      <th>Capacity</th>
      <th width="10%">Room Number/s</th>
      <th width="10%">Rate</th>
      <th width="10%">Action</th>
    </thead>
    <tbody>
<?php
$result = $db->query('SELECT * FROM room_types ORDER BY capacity ASC');

while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['name']}</td>";
  echo "
<td>
  <div class='bbImage'>
    <a href='{$main_url}image/rooms/{$row['filename']}' data-caption='{$row['name']}'>
      <img src='{$main_url}image/rooms/{$row['filename']}' style='height:100px;width:100px;object-fit:cover'>
    </a>
  </div>
</td>";
  echo "<td>{$row['description']}</td>";
  echo "<td>{$row['capacity']}</td>";
  echo '<td>' . join('<br>', getAllRoomsInType($row['roomTypeID'])) . '</td>';
  echo '<td>' . pesoFormat($row['rate']) . '</td>';
  echo "
    <td>
      <button data-id='{$row['roomTypeID']}' class='btn btn-primary btn-xs btn-block btnEditRoomType' style='min-width:100px'>Edit</button>
      <button data-id='{$row['roomTypeID']}' class='btn btn-primary btn-xs btn-block btnDeleteRoomType' style='min-width:100px'>Delete</button>
    </td>
  ";
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
