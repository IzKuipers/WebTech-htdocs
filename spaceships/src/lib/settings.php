<?php

require_once "component.php";
require_once __DIR__ . "/../session.php";

class SettingsDialog extends Component
{
  public function __construct()
  {
    parent::__construct("css/components/settings.css");

    $this->name = "SettingsDialog";

    $this->_renderComponent();
  }

  public function render(): string
  {
    global $sessionManager;

    $user = $sessionManager->me();

    if (!$user)
      return "";

    $username = $user["username"];

    if (!isset($_GET['settings']))
      return "";

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