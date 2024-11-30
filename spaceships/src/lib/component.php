<?php

/**
 * COMPONENT
 * 
 * This base class is a custom UI component thing I threw together
 * to reduce the amount of headaches UI in PHP causes.
 */

class Component
{
  public string $name = "";
  public string $stylesheet = "";

  public function __construct(string $stylesheet = "")
  {
    $this->stylesheet = $stylesheet; // Set the stylesheet
  }

  // This function renders the component and echoes it to the HTML.
  public function _renderComponent()
  {
    $result = "<!-- BEGIN $this->name -->"; // The resulting HTML

    // If there's a stylesheet, append it to the HTML now
    if ($this->stylesheet) {
      $result .= (string) "<link rel='stylesheet' href='" . $this->stylesheet . "'>";
    }

    // Call the render function and append it to the HTML
    $result .= $this->render();

    $result .= "<!-- END $this->name -->"; // Add close comment

    echo $result; // Echo the render output
  }

  // This function is supposed to return a fully rendered component.
  public function render()
  {
    return "PLACEHOLDER"; // Placeholder: to be replaced by derived components
  }
}