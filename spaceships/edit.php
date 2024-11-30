<?php

require_once "vendor/autoload.php";
require_once "src/SessionManager.php";
require_once "src/db/ShipStorage.php";

$sessionManager->checkIfLoggedIn(); // Check if we're logged in
$user = $sessionManager->me(); // Get the user

// If the user submitted an update...
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  // Missing one of the fields? Invalid request, go to index.
  if (!isset($_GET["id"], $_POST["name"], $_POST["description"])) {
    header("Location:index.php");

    die;
  }

  $id = $_GET["id"]; // Get the ID
  $name = $_POST["name"]; // Get the name of the ship
  $description = $_POST["description"]; // Get the ship's description

  $ship = $shipStorage->getShipById($id);

  if (!$ship) {
    header("Location:index.php");

    die;
  }

  // Check if the user and ship exist, and if the user owns the ship
  if (!$user || (int) $user["id"] !== (int) $ship["authorId"]) {
    header("Location:viewship.php?id=$id");
  }

  // Let's now update the ship
  $shipStorage->updateShip($ship["id"], $name, $description);
}

// Go to the index
Dialog(ErrorMessages::ShipUpdated, "viewship.php?id=$id");
header("Location:viewship.php?id=$id");

