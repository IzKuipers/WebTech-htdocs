<?php
require_once "src/header.php";
require_once "src/shiplist.php";
require_once "src/adddialog.php";
require_once "src/settings.php";
require_once "src/ship.php";
require_once "src/session.php";
require_once "src/error.php";

showError();

$sessionManager->checkIfLoggedIn();
$user = $sessionManager->me();
$ships = $shipStorage->getAllShips();
$sessionManager->checkForRmAccRequest();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/base.css">
  <title>Spaceships</title>
</head>

<body>
  <?php new HeaderBar(true) ?>
  <main>
    <?php new ShipList($ships) ?>
  </main>
  <?php new AddDialog();
  new SettingsDialog() ?>
</body>

</html>