<?php if (!defined('REMOVE_FOOTER')): ?>
<footer>
  <div class="col-md-4" align="center" style="border-right: 1px solid #CCC;">
    <img src="image/orig-logo.jpg" style="height: 100px;" />
  </div>
  <div class="col-md-4" align="left" style="border-right: 1px solid #CCC;">
    <i class="fa fa-mobile footer-icon"></i> (+63) 916 234 1234 <br>
    <i class="fa fa-phone footer-icon"></i> (046) 417 12 34 <br>
    <i class="fa fa-map-pin footer-icon"></i> Tambo M. Kulit, Indang, Cavite
  </div>
  <div class="col-md-4" align="center">
    <br>
    <a href="https://www.facebook.com/riovillanuevoresort/">
      <i class="fa fa-facebook-square" style="font-size:30px;color: #1abc9c"></i><br>
      Rio Villa Nuevo Official
    </a>
  </div>
</footer>
<?php endif;?>
<script src="dist/packages.js"></script>
<?php if (getenv('DEBUG') == 'true'): ?>
<?php
foreach ($jslist as $js) {
  echo "<script src=\"{$js}\"></script>\n";
}
?>
<?php else: ?>
  <script src="<?php echo $jspath; ?>?v=<?php echo filesize($jspath); ?>"></script>
<?php endif;?>
</body>
</html>
