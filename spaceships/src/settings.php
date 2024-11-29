<?php

require_once "component.php";
require_once "session.php";

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
          <p>To delete your Spaceships account, click the below button. This will delete your account, but it won't delete the spaceships you've added.</p>
          <a href="index.php?rmacc">
            <button class="delete-account">Delete Account</button>
          </a>
        </div>
      </div>
    HTML;
  }
}