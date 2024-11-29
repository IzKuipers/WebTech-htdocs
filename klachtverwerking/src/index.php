<?php

require_once "./mailer.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!isset($_POST["name"],$_POST["email"],$_POST["message"])) {
    echo "Missing name, email or message. Don't know which.";

    die;
  }

  $name = $_POST["name"];
  $email = $_POST["email"];
  $message = $_POST["message"];

  sendComplaint($name, $email, $message);

  header("Location: ./index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>Klachtformulier</h1>
  <p>Heeft u een klacht? Vul dit formulier in en we nemen zo snel als mogelijk contact met u op:</p>
  <form action="" method="POST">
    <label for="name">Uw naam:</label>
    <input type="text" name="name" id="name">
    <label for="email">Uw e-mailadres:</label>
    <input type="email" name="email" id="email"> 
    <label for="message">Uw klacht:</label>
    <textarea name="message" id="message"></textarea>
    <button type="submit">Klacht indienen</button>
  </form>
</body>
</html>