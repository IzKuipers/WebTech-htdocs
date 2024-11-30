<?php

require_once "component.php";
require_once __DIR__ . "/../util.php";

class ShipList extends Component
{
  public array $ships = [];
  public bool $linkToUser = true;

  public function __construct(array $ships, bool $linkToUser = true)
  {
    parent::__construct("css/components/shiplist.css");

    $this->ships = $ships;
    $this->name = "ShipList";
    $this->linkToUser = $linkToUser;

    $this->_renderComponent();
  }

  public function render()
  {
    $result = "<div class='ship-list'>";

    if (count($this->ships) === 0) {
      $result .= $this->_noShips();
    } else {
      foreach ($this->ships as $ship) {
        $result .= $this->_ship($ship);
      }
    }

    $result .= "</div>";

    return $result;
  }

  private function _ship(array $ship)
  {
    $image = base64_encode($ship["image"]);
    $name = $ship["name"];
    $id = $ship["id"];
    $description = $ship["description"];
    $authorName = $ship["authorName"];
    $authorId = $ship["authorId"];
    $timestamp = date("j M · G:i", strtotime($ship["timestamp"]));
    $shortDescription = truncateString($description, 32);

    $authorLink = $this->linkToUser ? "<a href='viewuser.php?id=$authorId'>$authorName</a>" : $authorName;

    $imageElement = "<div class='image' style='--src: url(\"data:image/png;base64,$image\");'></div>";
    $nameElement = "<h1 class='name'><a href='viewship.php?id=$id'>$name</a></h1>";
    $descriptionElement = "<p class='description'>$shortDescription</h1>";
    $authorBit = <<<HTML
      <div class='author'>
        <div class="pill author-name"> 
          $authorLink
        </div>
        <div class="pill timestamp">
          $timestamp
        </div>
      </div>
    HTML;

    $result = <<<HTML
      <div class='ship'>
        $imageElement
        $nameElement
        $descriptionElement
        $authorBit
      </div>
    HTML;

    return $result;
  }

  private function _noShips()
  {
    return <<<HTML
      <div class='no-ships'>
        <span class="material-icons">rocket_launch</span>
        <p>There's nothing here!</p>
      </div>
    HTML;
  }
}