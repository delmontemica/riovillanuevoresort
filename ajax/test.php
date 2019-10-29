<?php
use Dompdf\Dompdf;

// Account details
require_once '../backend.php';
$id = 1;

$domPDF = new Dompdf($domPDFOptions);
$domPDF->setPaper('letter');
$domPDF->setBasePath(__DIR__ . '/..');
$domPDF->loadHtml(require __DIR__ . '/../assets/' . $_GET['type'] . '.php');
$domPDF->render();

$domPDF->stream('test.pdf', [
  'Attachment' => false
]);
?>
