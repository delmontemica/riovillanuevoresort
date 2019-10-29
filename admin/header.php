<?php
@session_start();
require_once '../backend.php';

if ((!isset($_SESSION['admin']) || !hasPrivilege('Front-desk')) && !isset($login)) {
  echo "<script>location.href='login.php'</script>";
  die();
} else if (!isset($login)) {
  checkUpdatedStatus(true);
}

$csspath = '../dist/adminMain.css';
$csslist = [
  '../css/admin.css'
];

$jspath = '../dist/adminMain.js';
$jslist = [
  '../js/socket.js',
  '../js/admin.js'
];

require_once '../minifier.php';
?>
<!DOCTYPE html>
<html lang="en" ng-app="myApp">
<head>
  <title ng-bind="'Admin | ' + title"><?php echo $title ?? 'Admin'; ?></title>
  <base href="<?php echo $base_url_admin; ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="token" content="<?php echo encrypt(getToken()); ?>">
  <link rel="shortcut icon" href="<?php echo $main_url; ?>favicon.ico">
  <link rel="stylesheet" href="<?php echo $main_url; ?>dist/adminPackages.css">
<?php if (getenv('DEBUG') == 'true'): ?>
<?php
foreach ($csslist as $css) {
  $css = substr($css, 3);
  echo "<link rel=\"stylesheet\" href=\"{$main_url}{$css}\">\n";
}
?>
<?php else: ?>
  <link rel="stylesheet" href="<?php echo $main_url . substr($csspath, 3); ?>">
<?php endif;?>
</head>
<body>
  <div id="loadingMode">
    <img src="<?php echo $main_url; ?>image/logo2.png"/>
  </div>
