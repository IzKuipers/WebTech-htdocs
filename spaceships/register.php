<?php

require_once("src/auth.php");
require_once("src/error.php");
require_once("vendor/autoload.php");
require_once("src/error.php");

showError();

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

  if ($confirm != $password) {
    Dialog(ErrorMessages::PasswordMismatch);

    die;
  }

  $created = $authManager->createUser($username, $password);

  if ($created) {
    $_SESSION["toast"] = 1;
    header("Location: login.php");
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
      <input type="text" id="username" name="username" spellcheck="false" required>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required>
      <label for="password">Confirm password:</label>
      <input type="password" name="confirm" id="confirm" required>
      <input type="submit" value="Register">
      <a href="login.php" class="no-account">Have an account?</a>
    </form>
  </div>
</body>

</html>