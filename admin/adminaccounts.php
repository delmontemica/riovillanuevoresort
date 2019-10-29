<?php require_once '../backend.php';?>
<div style="overflow:auto;width:100%">
  <button class='btn btn-primary btn-xs pull-right' style="min-width:100px;margin-bottom:10px" data-toggle="modal" data-target="#registerModal">Add</button>
</div>
<div class="table-responsive">
  <table class="dt table table-hover" width="100%">
    <thead>
      <th>Username</th>
      <th>Type</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th width="15%">Status</th>
      <th>Action</th>
    </thead>
    <tbody>
<?php
$result = $db->query("SELECT * FROM {$table['admin']}");

while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['username']}</td>";
  echo "<td>{$row['type']}</td>";
  echo "<td>{$row['firstName']}</td>";
  echo "<td>{$row['lastName']}</td>";
  echo '<td>';
  if (hasPrivilege('Admin') && $row['username'] != getAdminInfo()['username']) {
    echo "<input data-id='{$row['username']}' data-type='admin' type='checkbox' name='chkStatus'" . ($row['status'] ? ' checked' : '') . ' data-toggle="toggle" data-size="small">';
  }
  echo '</td>';
  echo '<td>';
  if (hasPrivilege('Admin') && $row['username'] != getAdminInfo()['username']) {
    echo "<button data-id='{$row['username']}' class='btn btn-primary btn-xs btn-block btnEditAccountType'>Edit</button>";
  }
  echo '</td>';
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
