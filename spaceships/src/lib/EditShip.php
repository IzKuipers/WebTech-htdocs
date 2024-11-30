<?php

namespace Spaceships\Lib;

class EditShip extends Component
{
  public $shipId;
  public array $ship;

  public function __construct(array $ship)
  {
    parent::__construct("css/components/editship.css");

    $this->name = "EditShip";
    $this->shipId = $ship["id"];
    $this->ship = $ship;

    $this->_renderComponent();
  }

  public function render()
  {
    if (!isset($_GET["edit"])) {
      return "";
    }

    $currentName = $this->ship["name"];
    $currentDescription = $this->ship["description"];

    return <<<HTML
      <div class='dialog-wrapper'>
        <div class='dialog edit-ship'>
          <h1>
            <span>Edit ship</span>
            <a href="viewship.php?id=$this->shipId">
              [x]
            </a>
          </h1>
          <form action="edit.php?id=$this->shipId" method="POST">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required value="$currentName">
            <label for="description">Description</label>
            <textarea name="description" id="description" value="$currentDescription">$currentDescription</textarea>
            <input type="submit" value="Save changes">
          </form>
        </div>
      </div>
    HTML;
  }
}