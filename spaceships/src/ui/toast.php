<?php
require_once __DIR__ . "/../SessionManager.php";
require_once __DIR__ . "/../db/AuthorizationManager.php";

// This function displays a toast message if set
function displayToastMessage()
{
  global $authManager; // AuthorizationManager
  global $sessionManager; // SessionManager

  $sessionManager->trySessionStart(); // Let's start the session

  // No toast message? Stop.
  if (!isset($_SESSION["toast"])) {
    return;
  }

  // Get and remove the ID from the session
  $id = $_SESSION["toast"];
  unset($_SESSION["toast"]);

  try {
    $connection = $authManager->connect(); // Connect to the database

    $icon = "check_circle"; // Toast icon
    $message = ""; // Toast message

    // Query: Select all columns from toast where the ID matches ?
    $query = "SELECT * FROM toast WHERE id = ?";

    $statement = $connection->prepare($query); // Prepare the query
    $statement->bind_param("i", $id); // Bind the ID to ?

    // Execute the statement, and throw an error if it failed.
    if (!($statement->execute()))
      throw new Exception();

    $statement->bind_result($id, $message, $icon, $type); // Bind the result
    $statement->fetch(); // Fetch the result

    // No message? Then fall back to a default.
    if (!$message) {
      $message = "Success";
    }
  } catch (Exception $e) {
    $message = $e->getMessage(); // Display the database error as a toast
    $type = "error"; // Set the toast type to error
  } finally {
    $authManager->disconnect($connection, $statement); // Close the connection and statement
  }

  echo <<<HTML
      <script defer>
        document.addEventListener("DOMContentLoaded", () => {
          const wrapper = document.getElementById("toastWrapper");

          if (!wrapper) return;

          setTimeout(() => {
            wrapper.children[0].classList.add("visible");
          }, 100);

          setTimeout(() => {
            wrapper.children[0].classList.remove("visible");
          }, 3100);
        });
      </script>
      <div class="toast-wrapper visible" id="toastWrapper">
        <div class="toast $type">
          <span class="material-icons">$icon</span>
          <span class="message">$message</span>
        </div>
      </div>
  HTML;
}