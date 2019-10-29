<?php require_once '../backend.php';?>

<div class="table-responsive">
  <table class="dt table table-hover" width="100%" data-sort-by="desc">
    <thead>
      <th width="10%">ID</th>
      <th width="70%">Message</th>
      <th width="20%">Timestamp</th>
    </thead>
    <tbody>
<?php
$result = $db->query('SELECT * FROM notification');
while ($row = $result->fetch_assoc()):
?>
      <tr>
        <td><?php echo $row['ID']; ?></td>
        <td><?php echo $row['message']; ?></td>
        <td><?php echo dateFormat($row['timestamp'], 'M d, Y h:i:s A'); ?></td>
      </tr>
<?php endwhile;?>
    </tbody>
  </table>
</div>
