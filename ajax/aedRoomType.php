<?php
require_once '../backend.php';
supplyHeaders();

if (verifyToken()) {
  parse_str($_POST['data'], $data);

  if ($_POST['type'] == 'add') {
    echo addRoomType([
      'name'        => escape($data['txtName']),
      'description' => escape($data['txtDescription']),
      'feature'     => escape($data['txtFeature']),
      'capacity'    => escape($data['txtCapacity']),
      'rate'        => escape($data['txtRate'])
    ], explode("\n", $data['txtRoomNumber']), $_FILES['file'] ?? null);
  } else if ($_POST['type'] == 'edit') {
    echo editRoomType($data['roomTypeID'], [
      'name'        => escape($data['txtName']),
      'description' => escape($data['txtDescription']),
      'feature'     => escape($data['txtFeature']),
      'capacity'    => escape($data['txtCapacity']),
      'rate'        => escape($data['txtRate'])
    ], explode("\n", $data['txtRoomNumber']), $_FILES['file'] ?? null);
  } else if ($_POST['type'] == 'delete') {
    echo deleteRoomType($data['roomTypeID']);
  }
} else {
  echo 'Invalid Token.';
}
?>
