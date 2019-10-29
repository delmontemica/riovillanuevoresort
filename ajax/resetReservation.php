<?php
require_once '../backend.php';

$tables = [
  'reservation_bank',
  'reservation_cancelled',
  'reservation_check',
  'reservation_expense',
  'reservation_room',
  'reservation_transaction',
  'reservation'
];

if (isLogged(true) && hasPrivilege('Admin')) {
  foreach ($tables as $table) {
    $db->query("DELETE FROM {$table}");
    $db->query("ALTER TABLE {$table} AUTO_INCREMENT = 1");
  }
} else {
  echo 'Invalid Token.';
}
?>
