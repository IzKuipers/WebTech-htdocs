<?php
require_once "src/db/auth.php";
require_once "src/session.php";
require_once "vendor/autoload.php";
require_once "src/ui/error.php";

showError();

// Are we already logged in? Then go to the index page.
if (isset($_COOKIE["token"]) && $sessionManager->isLoggedIn($_COOKIE["token"])) {
  header("Location: index.php");

  die;
}

// If user posted their login information...
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  // No username or no password? Refresh, invalid request.
  if (!isset($_POST["username"], $_POST["password"])) {
    header("Location: login.php"); // Switches from POST to GET

    die;
  }

  $username = $_POST["username"]; // Get the username
  $password = $_POST["password"]; // Get the password

  $token = $authManager->authenticateUser($username, $password); // Try to authenticate the user

  // No token? Then the login failed and authenticateUser
  // already informed the user, so no need for further action.
  if (!$token)
    die;

  $sessionManager->trySessionStart(); // Let's start the session
  $_SESSION["toast"] = 2; // Set the 'You're logged in' toast message

  setcookie("token", $token, time() + (86400 * 30 * 7)); // Save the token to the cookies
  header("Location: index.php"); // Redirect to the index page
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
      <input type="text" id="username" name="username" spellcheck="false" required>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required>
      <input type="submit" value="Login">
      <a href="register.php" class="no-account">No account?</a>
    </form>
  </div>
</body>

</html>