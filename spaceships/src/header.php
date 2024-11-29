<?php

require_once "component.php";
require_once "session.php";

class HeaderBar extends Component
{
  public bool $addButton = false;

  public function __construct(bool $addButton = false)
  {
    parent::__construct("css/components/headerbar.css");

    $this->addButton = $addButton;
    $this->name = "HeaderBar";

    $this->_renderComponent();
  }

  public function render()
  {
    global $sessionManager;

    $user = $sessionManager->me();
    $username = $user["username"];
    $result = <<<HTML
      <header>
        <h1>
          <a href="index.php">Spaceships&#8482;</a>
        </h1>
    HTML;

    if ($this->addButton) {
      $result .= <<<HTML
        <a href="add.php" class="add-ship">
          <button>
            Add ship...
          </button>
        </a>
      HTML;
    }

    if ($user) {
      $result .= <<<HTML
        <div class="account-area">
          <span class="username">$username</span>&nbsp;&mdash;&nbsp;<a href="logout.php" class="logout">Logout</a>
        </div>
      HTML;
    }

    $result .= "</header>";

    return $result;
  }
}