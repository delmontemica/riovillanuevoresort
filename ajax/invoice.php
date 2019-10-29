<?php
use Dompdf\Dompdf;

require_once '../backend.php';

$domPDF = new Dompdf($domPDFOptions);
$domPDF->setBasePath('../');
$domPDF->loadHtml(require '../assets/invoice.php');
$domPDF->render();
$domPDF->stream('test.pdf', [
  'Attachment' => false
]);
?>
