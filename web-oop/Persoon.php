<?php

class Persoon
{
  public string $naam;

  public function __construct(string $naam)
  {
    $this->naam = $naam;
  }

  public function export(): array
  {
    return [];
  }
}