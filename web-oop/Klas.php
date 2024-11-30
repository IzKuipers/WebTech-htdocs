<?php

require_once "Docent.php";
require_once "Student.php";

class Klas
{
  public string $klasnaam;
  public Docent $docent;
  public array $studenten;

  public function __construct(string $klasnaam, Docent $docent, Student ...$studenten)
  {
    $this->klasnaam = $klasnaam;
    $this->docent = $docent;
    $this->studenten = $studenten;
  }

  public function export()
  {
    $studenten = [];

    foreach ($this->studenten as $student) {
      array_push($studenten, $student->export());
    }

    return [
      "docent" => $this->docent->export(),
      "studenten" => $studenten,
      "klasnaam" => $this->klasnaam
    ];
  }

  public function geefWeer()
  {
    $aantalStudenten = count($this->studenten);
    $docentNaam = $this->docent->naam;

    $html = "Klas $this->klasnaam van $docentNaam: $aantalStudenten student(en)<br>";

    //$html;
    return $html;
  }
}