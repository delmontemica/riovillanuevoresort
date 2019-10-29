<?php
require_once '../backend.php';
supplyHeaders();

logout(isset($_GET['admin']) ? true : false);
?>
