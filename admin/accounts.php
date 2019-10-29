<?php require_once '../backend.php';?>
<div class="table-responsive">
  <table class="dt table table-hover" width="100%">
    <thead>
      <th>Email Address</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Contact Number</th>
      <th>Verified</th>
      <th width="15%">Status</th>
    </thead>
    <tbody>
<?php
$result = $db->query("SELECT * FROM {$table['guest']}");

while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['emailAddress']}</td>";
  echo "<td>{$row['firstName']}</td>";
  echo "<td>{$row['lastName']}</td>";
  echo "<td>{$row['contactNumber']}</td>";
  echo '<td>' . ($row['verified'] ? 'Yes' : 'No') . '</td>';
  echo '<td>';
  if (hasPrivilege('Admin')) {
    echo "<input data-id='{$row['emailAddress']}' data-type='user' type='checkbox' name='chkStatus'" . ($row['status'] ? ' checked' : '') . ' data-toggle="toggle" data-size="small">';
  }
  echo '</td>';
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
