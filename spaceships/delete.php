<?php

require_once "vendor/autoload.php";
require_once "src/SessionManager.php";
require_once "src/db/ShipStorage.php";

$sessionManager->checkIfLoggedIn(); // Check if we're logged in
$user = $sessionManager->me(); // Get the user

// If the user requested a delete...
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  // Missing ID? Invalid request, go to index.
  if (!isset($_POST["id"])) {
    header("Location:index.php");

    die;
  }

  $id = $_POST["id"]; // Get the ID

  $ship = $shipStorage->getShipById($id);

  if (!$ship) {
    header("Location:index.php");

    die;
  }

  // Check if the user exists, and if the user owns the ship
  if (!$user || (int) $user["id"] !== (int) $ship["authorId"]) {
    header("Location:viewship.php?id=$id");
  }

  // Let's now update the ship
  $shipStorage->deleteShip($id);
}

// Go to the index
Dialog(ErrorMessages::ShipDeleted, "index.php");
header("Location:index.php");

