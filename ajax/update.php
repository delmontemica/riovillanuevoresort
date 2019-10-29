<?php
require_once '../backend.php';

supplyHeaders();

if (stripos('riovillanuevoresort.com', $_SERVER['SERVER_NAME']) !== false && hasPrivilege('Admin')) {
  putenv('PATH=/usr/local/cpanel/3rdparty/bin:/usr/bin:~/.nvm/versions/node/v8.11.4/bin');
  if ($_GET['reset'] == 'true') {
    echo shell_exec('git reset --hard 2>&1');
  }
  echo shell_exec('git pull && cd ../ && node build 2>&1');
} else {
  echo false;
}

?>
