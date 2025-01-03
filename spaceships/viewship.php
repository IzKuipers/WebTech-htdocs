<?php

require_once "vendor/autoload.php";
require_once "src/SessionManager.php";
require_once "src/ui/error.php";
require_once "src/lib/HeaderBar.php";
require_once "src/db/ShipStorage.php";

use Spaceships\Lib\DeleteShip;
use Spaceships\Lib\EditShip;
use Spaceships\Lib\HeaderBar;

$sessionManager->checkIfLoggedIn(); // Check if we're logged in
$user = $sessionManager->me(); // Get the user via their token

$sessionManager->trySessionStart(); // Let's start the session

showError();

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

// Determines if the current user owns the requested ship
$userOwnsShip = $user["id"] == $ship["authorId"];
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
    <div class="author">By <a
        href="viewuser.php?id=<?= $ship["authorId"] ?>"><?= $userOwnsShip ? "You" : $ship['authorName'] ?></a> on
      <?= date("j F Y \a\\t G:i", strtotime($ship["timestamp"])) ?>
    </div>

    <?php if ($userOwnsShip): ?>
      <div class="manage-ship">
        <a href="viewship.php?id=<?= $ship["id"] ?>&delete">
          <button class="delete">Delete</button>
        </a>
        <a href="viewship.php?id=<?= $ship["id"] ?>&edit">
          <button>Edit...</button>
        </a>
      </div>
    <?php endif ?>

    <div class="image" style="--src: url('data:image/png;base64,<?= base64_encode($ship["image"]) ?>');"></div>
    <p><?= $ship["description"] ?></p>

    <?php if ($userOwnsShip): ?>
      <?php new EditShip($ship) ?>
      <?php new DeleteShip($ship) ?>
    <?php endif ?>
  </main>
</body>

</html>