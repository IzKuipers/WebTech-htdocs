<?php

require_once "src/session.php";

$sessionManager->checkIfLoggedIn();
$user = $sessionManager->me();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/base.css">
  <title>Document</title>
</head>

<body>
  Hi, <?= $user["username"] ?>!
</body>

</html>