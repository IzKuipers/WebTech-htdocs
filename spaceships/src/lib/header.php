<?php

require_once "component.php";
require_once __DIR__ . "/../session.php";

class HeaderBar extends Component // is a derivative of the Component base class
{
  public bool $addButton = false;

  public function __construct(bool $addButton = false)
  {
    parent::__construct("css/components/headerbar.css"); // Construct the parent

    $this->addButton = $addButton; // Set addButton
    $this->name = "HeaderBar"; // Set the name of the component

    $this->_renderComponent(); // Let's now render the component
  }

  public function render()
  {
    global $sessionManager; // SessionManager

    $user = $sessionManager->me(); // Get the current user

    if (!$user)
      return; // No user? return.

    $username = $user["username"]; // Get the username
    $result = <<<HTML
      <header>
        <h1>
          <a href="index.php">Spaceships&#8482;</a>
        </h1>
    HTML; // The resulting HTML

    // If we're allowed to show the add button, show it here.
    if ($this->addButton) {
      $result .= <<<HTML
        <a href="index.php?adddialog" class="add-ship">
          <button>
            Post Ship
          </button>
        </a>
      HTML;
    }

    // If there is a user, append the account area.
    if ($user) {
      $result .= <<<HTML
        <div class="account-area">
          <span class="username">$username</span>
          <a href="index.php?settings" class="settings material-icons">settings</a>
          <a href="logout.php" class="logout material-icons">logout</a>
        </div>
      HTML;
    }

    // Close the header
    $result .= "</header>";

    // Return the HTML
    return $result;
  }
}