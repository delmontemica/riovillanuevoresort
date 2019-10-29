<?php
require_once '../backend.php';
supplyHeaders();

echo sendEmail([
  'email'   => $_GET['email'],
  'subject' => 'XD',
  'body'    => 'XD'
]);
?>
