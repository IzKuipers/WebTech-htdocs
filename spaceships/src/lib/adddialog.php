<?php

require_once "component.php";

class AddDialog extends Component
{
  public function __construct()
  {
    parent::__construct("css/components/adddialog.css");

    $this->name = "AddDialog";

    $this->_renderComponent();
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
            <textarea type="text" name="description" id="description" required></textarea>
            <label for="image">Image</label>
            <input type="file" name="image" id="image" accept="ïmage/png, .png, .jpg, .gif" required>
            <input type="submit" value="Upload">
          </form>
        </div>
      </div>
    HTML;
  }
}