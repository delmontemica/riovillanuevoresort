<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once '../backend.php';

$options = new Options();
$options->setDpi(150);
$options->set('DOMPDF_ENABLE_REMOTE', true);
$options->set('defaultMediaType', 'all');
$options->set('isFontSubsettingEnabled', true);


$domPDF = new Dompdf($options);
$domPDF->setPaper('letter', 'landscape');
$domPDF->setBasePath('../');
$domPDF->loadHtml(require '../assets/reports.php');
$domPDF->render();
$domPDF->stream('test.pdf', [
  'Attachment' => false
]);
?>
