<?php
require_once __DIR__ . '/vendor/autoload.php';

use MatthiasMullie\Minify;

$csslist = array_merge($csslist, $includecss ?? []);
$jslist  = array_merge($jslist, $includejs ?? []);

if (getenv('DEBUG') == 'false') {
  $minifier = new Minify\CSS();

  foreach ($csslist as $css) {
    $minifier->add($css);
  }

  $minifier->minify($csspath);

  $minifier = new Minify\JS();

  foreach ($jslist as $js) {
    $js = str_replace('js/', 'build/', $js);
    $minifier->add($js);
  }

  $minifier->minify($jspath);
}
?>
