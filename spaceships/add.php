<?php

require_once "vendor/autoload.php";
require_once "src/session.php";

$sessionManager->checkIfLoggedIn(); // Check if we're logged in
$user = $sessionManager->me(); // Get the user

// If the user submitted a ship...
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  // Missing one of the fields? Invalid request, go to index.
  if (!isset($_FILES["image"], $_POST["name"], $_POST["description"])) {
    header("Location:add.php");

    die;
  }

  $image = $_FILES["image"]; // Get the image
  $name = $_POST["name"]; // Get the name of the ship
  $description = $_POST["description"]; // Get the ship's description

  if (!$user)
    return; // User object somehow not valid? Stop.

  // Create the ship
  $shipStorage->addShip($user, $name, $description, $image);
}

// Go to the index
header("Location:index.php");
