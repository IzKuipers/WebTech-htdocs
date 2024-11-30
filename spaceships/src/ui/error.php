<?php
require_once __DIR__ . "/../db/auth.php";
require_once __DIR__ . "/../session.php";
require_once "toast.php";

// This function displays an error dialog by its ID
function Dialog(ErrorMessages $id, string $continue = "")
{
  global $sessionManager; // SessionManager

  $sessionManager->trySessionStart(); // Let's start the session

  $requestUri = $_SERVER["REQUEST_URI"]; // Get the request URI
  $currentUrl = parse_url($requestUri); // Parse the URI
  $pagePath = $currentUrl['path']; // Get the current path

  $id = $id->value; // Get the value of the error message ID

  $_SESSION["error_id"] = $id; // Set the ID
  $_SESSION["continue"] = $continue; // Set the continue URL (for the Close button)

  header("location: $pagePath"); // Reload the page
}

function showError()
{
  global $sessionManager; // SessionManager
  global $authManager; // AuthorizationManager

  $sessionManager->trySessionStart(); // Let's start the session
  displayToastMessage(); // Display any potential toast message

  // No error ID or no continue? Stop.
  if (!isset($_SESSION["error_id"], $_SESSION["continue"])) {
    return;
  }

  $id = $_SESSION["error_id"]; // Get the dialog's ID
  $continue = $_SESSION["continue"]; // Get the continue URL

  unset($_SESSION["error_id"], $_SESSION["continue"]); // Remove ID and continue from the session

  $connection = $authManager->connect(); // Connect to the database

  $title = ""; // Dialog title
  $message = ""; // Dialog message

  try {
    // No connection? Error out.
    if (!$connection) {
      throw new Exception("Failed to connect to database");
    }

    // Query: Select every column from errors where the ID matches ?
    $query = "SELECT * FROM errors WHERE id = ?";

    $statement = $connection->prepare($query); // Prepare the query
    $statement->bind_param("i", $id); // Bind the ID to the ?

    // Execute the statement and error if it failed
    if (!($statement->execute()))
      throw new Exception();

    $statement->bind_result($id, $title, $message); // Bind the result
    $statement->fetch(); // Fetch the result

    if (!$title) { // No title? Then the error doesn't exist. Let the user know.
      $title = "Unknown error";
      $message = "An error occured, but the error ID isn't known to me. I don't know what to say! I'm sorry.";
    }
  } catch (Exception $e) {
    // Failed to get the error message, let the user know.
    $title = "Double error!";
    $message = "Error $id occured, but I couldn't find the error's information. I'm sorry for the inconvenience.";
  } finally {
    $authManager->disconnect($connection, $statement); // Close the connection and statement.
  }

  echo <<<HTML
    <link rel="stylesheet" href="css/error.css">
    <div class="dialog-wrapper">
      <div class="dialog error">
        <h1>$title</h1>
        <p>$message</p>
        <a href="$continue"><button>Close</button></a>
      </div>
    </div>
  HTML;
}

// This enumeration contains all error messages that are stored in the database. It should ALWAYS match the rows in the errors table.
enum ErrorMessages: int
{
  case UserNotFound = 1;
  case PasswordIncorrect = 2;
  case ConnectionFailed = 3;
  case PasswordMismatch = 4;
  case UserAlreadyExists = 5;
  case ShipNotFound = 6;
  case ReqUserNotFound = 7;
}