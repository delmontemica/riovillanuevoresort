<?php
if ($_SERVER['SERVER_NAME'] == 'riovillanuevoresort.com') {
  header('Location: https://admin.riovillanuevoresort.com');
}
$includejs = [
  '../js/controller.js',
  '../js/adminRouter.js',
  '../js/adminNavbar.js'
];

require_once 'header.php';
if (isLogged(true)) {
  require_once 'modal.php';
}
require_once 'navbar.php';
?>
<div id="loadingMode" style="background-position:40% 35%;display:none"></div>
<div class="content" ng-view autoscroll="true"></div>
<?php require_once 'footer.php';?>
