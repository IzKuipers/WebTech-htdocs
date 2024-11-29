<?php
require_once("src/auth.php");
require_once("vendor/autoload.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  if (!isset($_POST["username"], $_POST["password"])) {
    header("Location: login.php");
    die;
  }

  $username = $_POST["username"];
  $password = $_POST["password"];

  $authManager = new AuthorizationManager();

  $token = $authManager->authenticateUser($username, $password);

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/login.css">
  <title>Login | Spaceships</title>
</head>

<body>
  <div class="login">
    <h1>Spaceships</h1>
    <form action="" method="POST">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" spellcheck="false">
      <label for="password">Password:</label>
      <input type="password" name="password" id="password">
      <input type="submit" value="Login">
      <a href="/register.php" class="no-account">No account?</a>
    </form>
  </div>
</body>

</html>