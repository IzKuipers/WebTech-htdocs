<?php
require_once("auth.php");
require_once("session.php");
require_once("toast.php");

function Dialog(ErrorMessages $id, string $continue = "")
{
  global $sessionManager;

  $sessionManager->trySessionStart();

  $requestUri = $_SERVER["REQUEST_URI"];
  $currentUrl = parse_url($requestUri);
  $pagePath = $currentUrl['path'];

  $id = $id->value;

  $_SESSION["error_id"] = $id;
  $_SESSION["continue"] = $continue;

  header("location: $pagePath");
}

function showError()
{
  global $sessionManager;
  global $authManager;

  $sessionManager->trySessionStart();
  displayToastMessage();

  if (!isset($_SESSION["error_id"], $_SESSION["continue"])) {
    unset($_SESSION["error_details"]);

    return;
  }

  $id = $_SESSION["error_id"];
  $continue = $_SESSION["continue"];

  unset($_SESSION["error_details"], $_SESSION["error_id"], $_SESSION["continue"]);

  $connection = $authManager->connect();

  $title = "";
  $message = "";

  try {
    if (!$connection) {
      throw new Exception("Failed to connect to database");
    }

    $query = "SELECT * FROM errors WHERE id = ?";

    $statement = $connection->prepare($query);
    $statement->bind_param("i", $id);

    if (!($statement->execute()))
      throw new Exception();

    $statement->bind_result($id, $title, $message);
    $statement->fetch();

    if (!$title) {
      $title = "Unknown error";
      $message = "An error occured, but the error ID isn't known to me. I don't know what to say! I'm sorry.";
    }
  } catch (Exception $e) {
    $title = "Double error!";
    $message = "Error $id occured, but I couldn't find the error's information. I'm sorry for the inconvenience.";
  } finally {
    $authManager->disconnect($connection, $statement);
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