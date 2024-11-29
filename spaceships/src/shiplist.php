<?php

require_once "component.php";

class ShipList extends Component
{
  public array $ships;
  public function __construct(array $ships)
  {
    parent::__construct("css/components/shiplist.css");

    $this->ships = $ships;
    $this->name = "ShipList";

    $this->_renderComponent();
  }

  public function render()
  {
    $result = "<div class='ship-list'>";

    foreach ($this->ships as $ship) {
      $result .= $this->_ship($ship);
    }

    $result .= "</div>";

    return $result;
  }

  private function _ship(array $ship)
  {
    $image = base64_encode($ship["image"]);
    $name = $ship["name"];
    $description = $ship["description"];
    $authorName = $ship["authorName"];
    $timestamp = date("j M · G:i", strtotime($ship["timestamp"]));

    $imageElement = "<div class='image' style='--src: url(\"data:image/png;base64,$image\");'></div>";
    $nameElement = "<h1 class='name'>$name</h1>";
    $descriptionElement = "<p class='description'>$description</h1>";
    $authorBit = <<<HTML
      <div class='author'>
        <div class="pill author-name"> 
          $authorName
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
}