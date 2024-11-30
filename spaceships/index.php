<?php
/**
 * Hoi Marcel :P
 * 
 * Comments zijn in het Engels omdat ik nou eenmaal in het Engels denk.
 * De GitHub commits tonen aan dat ik deze code ook daadwerkelijk heb geschreven.
 * 
 * https://github.com/IzKuipers/WebTech-htdocs/commits
 * 
 * Van toepassing in dit project:
 *  - Composer & PSR-4 (pending)
 *  - MySQLi
 *  - OOP/inheritance
 *  - File uploads
 *  - Authentication
 * 
 * Niet de originele opdracht, I know, maar het behaalt wel de leerdoelen van deze en
 * veel andere opdrachten van beide periode 1 en vorig schooljaar. Heb dagen lang een
 * game proberen te maken in PHP, maar dat werkt gewoon niet in mijn hoofd. Oeps.
 * 
 * - Izaak
 */
require_once "src/lib/header.php";
require_once "src/lib/shiplist.php";
require_once "src/lib/adddialog.php";
require_once "src/lib/settings.php";
require_once "src/db/ship.php";
require_once "src/session.php";
require_once "src/ui/error.php";

showError(); // Show potential error message

$sessionManager->checkIfLoggedIn(); // Check if we're logged in

$user = $sessionManager->me(); // Let's get the user...
$ships = $shipStorage->getAllShips(); // ...and all ships.

$sessionManager->checkForRmAccRequest(); // Check for a request to delete the user account
$sessionManager->checkForAccResetRequest(); // Check for a request to reset the user account

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