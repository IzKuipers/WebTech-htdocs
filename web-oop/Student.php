<?php

require_once "Persoon.php";

class Student extends Persoon
{
  public int $studentNummer;
  public bool $ingelogd = false;

  public function __construct(string $naam, int $studentNummer)
  {
    parent::__construct($naam);

    $this->studentNummer = $studentNummer;
  }

  public function inloggen(string $wachtwoord): bool
  {
    if ($this->ingelogd) {
      throw new Exception("Student kan maar op één plek ingelogd zijn");
    }

    $this->ingelogd = true;

    // Doe iets met het wachtwoord, met lengte checken kom je niet ver

    $loginResultaat = count_chars($wachtwoord) > 0;

    return $loginResultaat;
  }

  public function uitloggen(): void
  {
    if (!$this->ingelogd) {
      throw new Exception("Geen sessie om uit te loggen!");
    }

    $this->ingelogd = false;

    // doe iets met het verwijderen van een token ofzo
  }

  public function export(): array
  {
    return [
      "studentNummer" => $this->studentNummer,
      "naam" => $this->naam,
      "ingelogd" => $this->ingelogd
    ];
  }

  public function geefWeer()
  {
    $html = "Student $this->naam - $this->studentNummer<br>";

    //echo $html;
    return $html;
  }
}