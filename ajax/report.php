<?php
use Dompdf\Dompdf;

switch ($_GET['type']) {
case 'reservation':
  $title     = 'Reservation';
  $landscape = true;
  break;
case 'cancelled':
  $title = 'Cancelled Reservation';
  break;
case 'guestranking':
  $title = 'Guest Ranking';
  break;
case 'roomranking':
  $title = 'Room Ranking';
  break;
case 'sales':
  $title = 'Sales Report';
  break;
default:
  die('Invalid type.');
  break;
}

require_once '../backend.php';

$domPDF = new Dompdf($domPDFOptions);
$domPDF->setPaper('letter', isset($landscape) ? 'landscape' : 'portrait');
$domPDF->setBasePath('../');
$domPDF->loadHtml(require '../assets/report.php');
$domPDF->render();
$domPDF->stream('test.pdf', [
  'Attachment' => false
]);
?>
