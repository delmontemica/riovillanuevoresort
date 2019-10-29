<?php
$includejs = [
  'js/controller.js',
  'js/router.js'
];

require_once 'header.php';
require_once 'modal.php';
require_once 'navbar.php';
?>
<div id="loadingMode" style="z-index:1;display:none"></div>
<div class="content" ng-view autoscroll="true"></div>
<?php
require_once 'footer.php';
?>
