<?php

require_once "Persoon.php";

class Docent extends Persoon
{
  public string $afkorting;

  public function __construct(string $naam, string $afkorting)
  {
    parent::__construct($naam);

    $this->afkorting = $afkorting;
  }

  public function export(): array
  {
    return [
      "naam" => $this->naam,
      "afkorting" => $this->afkorting
    ];
  }

  public function geefWeer()
  {
    $html = "Docent $this->naam ($this->afkorting).<br>";

    //echo $html;
    return $html;
  }
}