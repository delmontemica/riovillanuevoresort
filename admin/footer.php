    </div>
  </div>
  <script>const main_url="<?php echo $main_url ?>";</script>
  <script src="<?php echo $main_url ?>dist/adminPackages.js"></script>
<?php if (getenv('DEBUG') == 'true'): ?>
<?php
foreach ($jslist as $js) {
  $js = substr($js, 3);
  echo "<script src=\"{$main_url}{$js}\"></script>\n";
}
?>
<?php else: ?>
  <script src="<?php echo $main_url . substr($jspath, 3); ?>?v=<?php echo filesize('../dist/adminMain.js'); ?>"></script>
<?php endif;?>
</body>
</html>
