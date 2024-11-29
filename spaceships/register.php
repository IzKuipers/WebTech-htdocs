<?php

namespace Spaceships;
use Spaceships\AuthorizationManager;

require_once("vendor/autoload.php");

if (isset($_COOKIE["token"]) && $sessionManager->isLoggedIn($_COOKIE["token"])) {
  header("Location: index.php");

  die;
}
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  if (!isset($_POST["username"], $_POST["password"], $_POST["confirm"])) {
    header("Location: register.php");
    die;
  }

  $username = $_POST["username"];
  $password = $_POST["password"];
  $confirm = $_POST["confirm"];

  try {
    if ($confirm != $password)
      throw new \Exception("Passwords don't match");

    $authManager = new AuthorizationManager();
    $authManager->createUser($username, $password);

    header("Location: login.php");
  } catch (\Exception $e) {
    // TODO
    echo "Failed to create user: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/login.css">
  <title>Register | Spaceships</title>
</head>

<body>
  <div class="login">
    <h1>Create Account</h1>
    <form action="" method="POST">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" spellcheck="false">
      <label for="password">Password:</label>
      <input type="password" name="password" id="password">
      <label for="password">Confirm password:</label>
      <input type="password" name="confirm" id="confirm">
      <input type="submit" value="Register">
      <a href="/login.php" class="no-account">Have an account?</a>
    </form>
  </div>
</body>

</html>