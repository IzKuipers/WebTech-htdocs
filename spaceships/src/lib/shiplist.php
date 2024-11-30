<?php

require_once "component.php";
require_once __DIR__ . "/../util.php";

class ShipList extends Component // is a derivative of the Component base class
{
  public array $ships = []; // The list of ships to display
  public bool $linkToUser = true; // Determines if we add a link to the user's profile to author-name pill

  public function __construct(array $ships, bool $linkToUser = true)
  {
    parent::__construct("css/components/shiplist.css"); // Construct the parent

    $this->ships = $ships; // Set the ships to be rendered
    $this->name = "ShipList"; // Set the name
    $this->linkToUser = $linkToUser; // Set linkToUser

    $this->_renderComponent(); // Let's now render the component
  }

  public function render()
  {
    $result = "<div class='ship-list'>"; // The resulting HTML

    // No ships? Append no-ships to the HTML
    if (count($this->ships) === 0) {
      $result .= $this->_noShips();
    } else {
      // Otherwise; for every ship of ships...
      foreach ($this->ships as $ship) {
        // ...Render the ship and append it to the HTML
        $result .= $this->_ship($ship);
      }
    }

    // Close the div
    $result .= "</div>";

    // Return the result
    return $result;
  }

  private function _ship(array $ship)
  {
    $image = base64_encode($ship["image"]); // The raw image data, encoded as base64
    $name = $ship["name"]; // The name of the ship
    $id = $ship["id"]; // The ID of the ship
    $description = $ship["description"]; // The description of the ship
    $authorName = $ship["authorName"]; // The name of the author
    $authorId = $ship["authorId"]; // The ID of the author
    $timestamp = date("j M Y · G:i", strtotime($ship["timestamp"])); // The date and time that the ship was posted
    $shortDescription = truncateString($description, 32); // Truncated version of the descripion with a max length of 32 characters

    $authorLink = $this->linkToUser ? "<a href='viewuser.php?id=$authorId'>$authorName</a>" : $authorName; // The author's name (a link if linkToUser is set)

    $imageElement = "<div class='image' style='--src: url(\"data:image/png;base64,$image\");'></div>"; // The ship's image
    $nameElement = "<h1 class='name'><a href='viewship.php?id=$id'>$name</a></h1>"; // the name element of the ship, with a link to viewship
    $descriptionElement = "<p class='description'>$shortDescription</h1>"; // The short description of the ship
    $authorBit = <<<HTML
      <div class='author'>
        <div class="pill author-name"> 
          $authorLink
        </div>
        <div class="pill timestamp">
          $timestamp
        </div>
      </div>
    HTML; // The author pills

    $result = <<<HTML
      <div class='ship'>
        $imageElement
        $nameElement
        $descriptionElement
        $authorBit
      </div>
    HTML; // The resulting HTML

    return $result; // Return the resulting HTML
  }

  // This function returns HTML for the no-ships div, in case the list is empty
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