<?php
require_once "src/header.php";
require_once "src/session.php";
require_once "src/ship.php";

$sessionManager->checkIfLoggedIn();
$user = $sessionManager->me();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  if (!isset($_FILES["image"], $_POST["name"], $_POST["description"])) {
    header("Location:add.php");

    die;
  }

  $image = $_FILES["image"];
  $name = $_POST["name"];
  $description = $_POST["description"];

  if (!$user)
    return;

  $shipStorage->addShip($user, $name, $description, $image);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/add.css">
  <title>Add Ship - Spaceships</title>
</head>

<body>
  <?php new HeaderBar(false) ?>
  <main class="add">
    <form action="" method="POST" enctype="multipart/form-data">
      <label for="image">Image</label>
      <input type="file" name="image" id="image" accept="ïmage/png, .png, .jpg, .gif" required>
      <label for="name">Name</label>
      <input type="text" name="name" id="name" required>
      <label for="description">Description</label>
      <textarea type="text" name="description" id="description" required></textarea>
      <input type="submit" value="Upload">
    </form>
  </main>
</body>

</html>