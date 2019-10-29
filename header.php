<?php
@session_start();
require_once 'backend.php';

if (isLogged()) {
  checkUpdatedStatus();
}

if (isset($_GET['admin'])) {
  $_SESSION['maintenance'] = false;
}

if ($maintenance && !isset($_SESSION['maintenance'])) {
  require 'maintenance.php';
  die();
}

$csspath = 'dist/main.css';
$csslist = [
  'css/navbar.css',
  'css/style.css'
];

$jspath = 'dist/main.js';
$jslist = [
  'js/socket.js',
  'js/script.js'
];
require_once 'minifier.php';
?>
<!DOCTYPE html>
<html lang="en" ng-app="myApp">
<head>
  <title ng-bind="'Rio Villa Nuevo | <?php echo !isset($title) ? "' + title" : $title . "'" ?>">Rio Villa Nuevo</title>
  <base href="<?php echo $base_url; ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="token" content="<?php echo encrypt(getToken()); ?>">
  <link rel="shortcut icon" href="favicon.ico">
  <link rel="stylesheet" href="dist/packages.css">
<?php if (getenv('DEBUG') == 'true'): ?>
<?php
foreach ($csslist as $css) {
  echo "  <link rel=\"stylesheet\" href=\"$css\">\n";
}
?>
<?php else: ?>
  <link rel="stylesheet" href="<?php echo $csspath; ?>">
<?php endif;?>
<?php if (isset($title) && ($title == 'Registration' || $title == 'Forgot Password')): ?>
  <script src='https://www.google.com/recaptcha/api.js'></script>
<?php endif?>
</head>
<body>
  <div id="loadingMode">
    <img src="image/logo2.png"/>
  </div>
  <a id="backToTop"><i class="fa fa-arrow-up fa-lg"></i></a>
