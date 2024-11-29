<?php

class Spaceship
{
  public $name = "";
  public int $health = 100;
  public int $fuel;
  public int $ammo;
  public bool $dead = false;

  public function __construct(int $ammo = 100, $fuel = 100, $hitPoints = 100)
  {
    $this->ammo = $ammo;
    $this->fuel = $fuel;
    $this->hitPoints = $hitPoints;
    $this->dead = false;
  }

  public function loadData(array $data)
  {
    $this->health = $data["health"];
    $this->fuel = $data["fuel"];
    $this->ammo = $data["ammo"];
    $this->dead = $data["dead"];
  }
}