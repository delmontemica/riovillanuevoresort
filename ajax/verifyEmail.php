<?php
require_once '../backend.php';
supplyHeaders();

if (verifyEmail(decrypt($_GET['token']))) {

  $message = 'Verified Successfully!';
} else {
  $message = 'Invalid Token!';
}
echo "<script>alert('{$message}');location.href='../'</script>";
?>
