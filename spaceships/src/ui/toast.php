<?php
require_once __DIR__ . "/../session.php";
require_once __DIR__ . "/../db/auth.php";

function displayToastMessage()
{
  global $authManager;
  global $sessionManager;

  $sessionManager->trySessionStart();

  if (!isset($_SESSION["toast"])) {
    return;
  }

  $id = $_SESSION["toast"];
  unset($_SESSION["toast"]);

  try {
    $connection = $authManager->connect();

    $icon = "check_circle";
    $message = "";

    $query = "SELECT * FROM toast WHERE id = ?";

    $statement = $connection->prepare($query);
    $statement->bind_param("i", $id);

    if (!($statement->execute()))
      throw new Exception();

    $statement->bind_result($id, $message, $icon, $type);
    $statement->fetch();

    if (!$message) {
      $message = "Success";
    }
  } catch (Exception $e) {
    $message = $e->getMessage();
    $type = "error";
  } finally {
    $authManager->disconnect($connection, $statement);
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