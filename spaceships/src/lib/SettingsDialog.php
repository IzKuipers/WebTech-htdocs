<?php

namespace Spaceships\Lib;

require_once __DIR__ . "/../SessionManager.php";

class SettingsDialog extends Component // is a derivative of the Component base class
{
  public function __construct()
  {
    parent::__construct("css/components/settings.css"); // Initialize the parent

    $this->name = "SettingsDialog"; // Set the name

    $this->_renderComponent(); // Let's now render the component
  }

  public function render(): string
  {
    global $sessionManager; // SessionManager

    // No settings request in the GET? Return an empty component.
    if (!isset($_GET['settings']))
      return "";

    $user = $sessionManager->me(); // Get the current user

    // No user? Return an empty component.
    if (!$user)
      return "";

    $username = $user["username"]; // Get the username

    return <<<HTML
      <div class="dialog-wrapper">
        <div class="dialog settings">
          <h1>
            <span>Account settings of $username</span>
            <a href="index.php">[x]</a>
          </h1>
          <p>To delete your Spaceships account, click the below button. This will delete your account and all of your ships.</p>
          <a href="index.php?rmacc">
            <button class="delete-account">Delete Account</button>
          </a>
          <hr>
          <p>You can use the following button to reset your account. This will log you out everywhere, and delete all of your ships, but it won't delete your account.</p>
          <a href="index.php?accreset">
            <button class="reset-account">Reset account</button>
          </a>
          <br>
        </div>
      </div>
    HTML;
  }
}