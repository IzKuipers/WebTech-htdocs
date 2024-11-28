<?php

require "../vendor/autoload.php";

use Dompdf\Dompdf;

$html = file_get_contents(__DIR__ . "\\sample.html");

$dompdf = new Dompdf();
$dompdf->loadHtml($html);

$dompdf->setPaper("A4","portrait");

$dompdf->render();
$dompdf->stream();