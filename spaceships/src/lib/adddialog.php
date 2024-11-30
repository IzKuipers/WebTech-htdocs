<?php

namespace Spaceships\Lib;

use Spaceships\Lib\Component;

class AddDialog extends Component // is a derivative of the Component base class
{
  public function __construct()
  {
    parent::__construct("css/components/adddialog.css"); // Construct the parent

    $this->name = "AddDialog"; // Set the name of the component

    $this->_renderComponent(); // Let's now render the component
  }

  public function render(): string
  {
    if (!isset($_GET['adddialog']))
      return "";

    return <<<HTML
      <div class="dialog-wrapper">
        <div class="dialog add-dialog">
          <h1>
            <span>Add a ship</span>
            <a href="index.php">[x]</a>
          </h1>
          <form action="add.php" method="POST" enctype="multipart/form-data">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required>
            <label for="description">Description</label>
            <textarea name="description" id="description" required></textarea>
            <label for="image">Image</label>
            <input type="file" name="image" id="image" accept="ïmage/png, .png, .jpg, .gif" required>
            <input type="submit" value="Upload">
          </form>
        </div>
      </div>
    HTML;
  }
}