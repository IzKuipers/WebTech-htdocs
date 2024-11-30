<?php
require_once "src/lib/header.php";
require_once "src/lib/shiplist.php";
require_once "src/lib/adddialog.php";
require_once "src/lib/settings.php";
require_once "src/db/ship.php";
require_once "src/session.php";
require_once "src/ui/error.php";

showError();

$sessionManager->checkIfLoggedIn();
$user = $sessionManager->me();
$ships = $shipStorage->getAllShips();
$sessionManager->checkForRmAccRequest();
$sessionManager->checkForAccResetRequest();

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