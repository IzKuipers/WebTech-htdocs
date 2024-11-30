<?php

require_once("src/db/auth.php");
require_once("src/ui/error.php");
require_once("vendor/autoload.php");

showError(); // Show a potential error message

// Is the user already logged in? Then redirect to the index page.
if (isset($_COOKIE["token"]) && $sessionManager->isLoggedIn($_COOKIE["token"])) {
  header("Location: index.php");

  die;
}

// If the user posted their registration info...
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  // Missing one of the values? Refresh, invalid request.
  if (!isset($_POST["username"], $_POST["password"], $_POST["confirm"])) {
    header("Location: register.php"); // Switches from POST to GET

    die;
  }

  $username = $_POST["username"]; // Get the username
  $password = $_POST["password"]; // Get the password
  $confirm = $_POST["confirm"]; // Get the password confirmation

  // Password mismatch? Tell the user and stop.
  if ($confirm != $password) {
    Dialog(ErrorMessages::PasswordMismatch);

    die;
  }

  // Let's now create the user
  $created = $authManager->createUser($username, $password);

  // If the user creation failed, it'll send a dialog in createUser.
  // Otherwise, set the 'Account created' toast and go to the login page.
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