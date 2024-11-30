<?php

namespace Spaceships\Lib;

class DeleteShip extends Component
{
  public $shipId;
  public array $ship;

  public function __construct(array $ship)
  {
    parent::__construct("css/components/deleteship.css");

    $this->name = "DeleteShip";
    $this->shipId = $ship["id"];
    $this->ship = $ship;

    $this->_renderComponent();
  }

  public function render()
  {
    if (!isset($_GET["delete"])) {
      return "";
    }

    $shipName = $this->ship["name"];

    return <<<HTML
      <div class='dialog-wrapper'>
        <div class='dialog delete-ship'>
          <h1>
            <span>Delete $shipName?</span>
          </h1>
          <p>Are you sure you want to delete the beautiful '$shipName' ship from Spaceships? This cannot be undone.</p>
          <form action="delete.php" method="POST">
            <input type="hidden" name="id" value="$this->shipId">
            <a href="viewship.php?id=$this->shipId" class="button">
              Cancel
            </a>
            <input type="submit" value="Delete" class="delete">
          </form>
          
        </div>
      </div>
    HTML;
  }
}