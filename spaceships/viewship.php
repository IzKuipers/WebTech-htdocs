<?php

require_once "src/session.php";
require_once "src/ui/error.php";
require_once "src/lib/header.php";
require_once "src/db/ship.php";

$sessionManager->checkIfLoggedIn(); // Check if we're logged in
$user = $sessionManager->me(); // Get the user via their token

$sessionManager->trySessionStart(); // Let's start the session

if (!isset($_GET["id"])) { // Is there no ID? Invalid request, go to index.
  header("location:index.php");
}

// Get the ship by the specified ID
$ship = $shipStorage->getShipById($_GET["id"]);

// No such ship? Display ship not found on index.php and stop.
if (count($ship) == 0) {
  Dialog(ErrorMessages::ShipNotFound, "index.php");

  die;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/viewship.css">
  <title><?= $ship["name"] ?> | Spaceships</title>
</head>

<body>
  <?php new HeaderBar() ?>
  <main class="view-ship">
    <h1><?= $ship["name"] ?></h1>
    <div class="author">By <a href="viewuser.php?id=<?= $ship["authorId"] ?>"><?= $ship['authorName'] ?></a> on
      <?= date("j F Y \a\\t G:i", strtotime($ship["timestamp"])) ?>
    </div>
    <div class="image" style="--src: url('data:image/png;base64,<?= base64_encode($ship["image"]) ?>');"></div>
    <p><?= $ship["description"] ?></p>
  </main>
</body>

</html>