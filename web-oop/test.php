<?php

require 'vendor/autoload.php';

require_once "Student.php";
require_once "Docent.php";
require_once "Klas.php";

use Dompdf\Dompdf;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$piet = new Student("Piet van Pietersen", 21917);
$jan = new Student("Jan van Dam", 71093);
$joop = new Student("Joop Janssen", 27196);

$marcel = new Docent("Marcel van Drunen", "mdrun");

$klas = new Klas("h2onweb2a", $marcel, $piet, $jan, $joop);

$klasObject = $klas->export();

$html = "";

foreach ($klas->studenten as $student) {
  $html .= $student->geefWeer();
}

$html .= $klas->docent->geefWeer();
$html .= $klas->geefWeer();

if (isset($_GET["export"])) {
  $dompdf = new Dompdf();
  $dompdf->loadHtml($html);

  $dompdf->setPaper("A4", "portrait");

  $dompdf->render();
  $dompdf->stream("document.pdf");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School informatie</title>
</head>

<body>
  <h1>School informatie</h1>
  <p><?= $html ?></p>
  <a href="test.php?export">Exporteer naar PDF</a>
</body>

</html>