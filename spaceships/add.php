<?php
require_once "src/session.php";
require_once "src/ship.php";

$sessionManager->checkIfLoggedIn();
$user = $sessionManager->me();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  if (!isset($_FILES["image"], $_POST["name"], $_POST["description"])) {
    header("Location:add.php");

    die;
  }

  $image = $_FILES["image"];
  $name = $_POST["name"];
  $description = $_POST["description"];

  if (!$user)
    return;

  $shipStorage->addShip($user, $name, $description, $image);
}

header("Location:index.php");
