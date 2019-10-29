<?php
use Dompdf\Dompdf;

require_once '../backend.php';

$domPDFOptions->setDPI(50);

$domPDF = new Dompdf($domPDFOptions);
$domPDF->loadHtml(require '../assets/receipt.php');
$domPDF->render();
$domPDF->stream('test.pdf', [
  'Attachment' => false
]);
?>
