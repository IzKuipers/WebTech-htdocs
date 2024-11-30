<?php

require_once "src/session.php";
require_once "src/ui/error.php";
require_once "src/lib/header.php";
require_once "src/db/ship.php";
require_once "src/lib/shiplist.php";

$sessionManager->checkIfLoggedIn(); // Check if we're logged in
$user = $sessionManager->me(); // Get the user

$sessionManager->trySessionStart(); // Let's star tthe session

if (!isset($_GET["id"])) { // Is there no ID? Invalid request, go to index.
  header("location:index.php");
}

// Get the user by the specified ID
$reqUser = $authManager->getUserById($_GET["id"]);

// No such user? Display user not found v2 on index.php and stop.
if (!$reqUser || count($reqUser) == 0) {
  Dialog(ErrorMessages::ReqUserNotFound, "index.php");

  die;
}

$userShips = $shipStorage->getShipsOfUser($reqUser); // Get the ships of the user
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/viewuser.css">
  <title>Ships of <?= $reqUser["username"] ?> | Spaceships</title>
</head>

<body>
  <?php new HeaderBar() ?>
  <main class="view-user">
    <div class="user-header">
      <div class="initials">
        <?= strtoupper(substr($reqUser["username"], 0, 2)) ?>
      </div>
      <h1 class="username"><?= $reqUser["username"] ?></h1>
    </div>
    <?php new ShipList($userShips, false) // Display the user's ships ?>
  </main>
</body>

</html>