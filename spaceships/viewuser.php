<?php

require_once "src/session.php";
require_once "src/ui/error.php";
require_once "src/lib/header.php";
require_once "src/db/ship.php";
require_once "src/lib/shiplist.php";

$sessionManager->checkIfLoggedIn();
$user = $sessionManager->me();

$sessionManager->trySessionStart();

if (!isset($_GET["id"])) {
  header("location:index.php");
}

$reqUser = $authManager->getUserById($_GET["id"]);

if (!$reqUser || count($reqUser) == 0) {
  Dialog(ErrorMessages::ReqUserNotFound, "index.php");

  die;
}

$userShips = $shipStorage->getShipsOfUser($reqUser);
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
    <?php new ShipList($userShips, false) ?>
  </main>
</body>

</html>