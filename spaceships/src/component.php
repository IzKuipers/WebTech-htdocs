<?php

class Component
{
  public string $name = "";
  public string $stylesheet = "";

  public function __construct(string $stylesheet = "")
  {
    $this->stylesheet = $stylesheet;
  }

  public function _renderComponent()
  {
    $result = "<!-- BEGIN $this->name -->";

    if ($this->stylesheet) {
      $result .= (string) "<link rel='stylesheet' href='" . $this->stylesheet . "'>";
    }

    $result .= $this->render();

    $result .= "<!-- END $this->name -->";

    echo $result;
  }

  public function render()
  {
    return "PLACEHOLDER";
  }
}