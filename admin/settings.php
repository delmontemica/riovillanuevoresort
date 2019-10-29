<?php require_once '../backend.php';?>

<a href="#/settings/roomtypes" class="btn btn-primary btn-lg btn-choice">ROOM TYPES<br>CONFIGURATION</a>
<?php if (hasPrivilege('Admin')): ?>
<a href="#/settings/logs" class="btn btn-primary btn-lg btn-choice">SYSTEM<br>LOGS </a>
<?php endif;?>
