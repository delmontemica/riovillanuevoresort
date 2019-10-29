<?php
require_once '../../../backend.php';

if (!hasPrivilege('Admin')) {
  echo "<script>alert('Not Allowed');history.back()</script>";
}
?>

<h6><a href="#/settings">← Back</a></h6>
<a class='btn btn-primary btn-xs pull-right' style="min-width:100px;margin-bottom:10px" href="#/settings/logs/admin">
  Admin
</a>
<div class="table-responsive">
  <table class="dt table table-hover" width="100%">
    <thead>
      <th width="10%">ID</th>
      <th width="30%">Email Address</th>
      <th width="40%">Action</th>
      <th width="20%">TimeStamp</th>
    </thead>
    <tbody>
<?php
$result = $db->query("
  SELECT * FROM logs WHERE type='User'
");

while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['ID']}</td>";
  echo "<td>{$row['name']}</td>";
  echo "<td>{$row['action']}</td>";
  echo '<td>' . dateFormat($row['timestamp'], 'M d, Y h:i:s A') . '</td>';
  echo '</tr>';
}
?>
    </tbody>
  </table>
</div>
