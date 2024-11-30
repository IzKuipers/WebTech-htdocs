<?php

require_once "src/session.php";
require_once "src/error.php";
require_once "src/header.php";
require_once "src/ship.php";

$sessionManager->checkIfLoggedIn();
$user = $sessionManager->me();

$sessionManager->trySessionStart();

if (!isset($_GET["id"])) {
  header("location:index.php");
}

$ship = $shipStorage->getShipById($_GET["id"]);

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
  <title>Document</title>
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