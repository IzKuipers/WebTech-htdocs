<?php

namespace Spaceships\Db;

use Exception;

/**
 * SHIP STORAGE
 * 
 * This class is responsible for the managing of ships between the site
 * and database: it creates, deletes and retrieves ships.
 */

class ShipStorage extends DatabaseModule
{
  public function __construct()
  {
    parent::__construct(); // Construct the parent as well
  }

  // This function gets all ships in the database, with the author's name joined to each row.
  public function getAllShips(): array
  {
    // Query: Get all ships, and add the username of each row's author to the row.
    $query = <<<SQL
      SELECT 
        ships.id,
        ships.name,
        ships.description,
        ships.image,
        users.username AS authorName,
        ships.authorId,
        ships.timestamp
      FROM ships
      INNER JOIN users 
      ON ships.authorId = users.id;
    SQL;

    try {
      $connection = $this->connect(); // Connect to the database

      // Execute the query and fetch all the results as associative array
      $result = $connection->query($query)->fetch_all(MYSQLI_ASSOC);

      return $result; // Return the result
    } catch (Exception $e) {
      return []; // Error occured, return empty array as dummy
    } finally {
      $this->disconnect($connection); // Close the connection
    }
  }

  // This function returns all ships of the specified user.
  public function getShipsOfUser(array $user)
  {
    $userId = $user["id"]; // Get the user's ID from the assoc

    // Query: Get all ships where the author's ID is equal to $userId, and add the username of each row's author to the row.
    $query = <<<SQL
      SELECT 
        ships.id,
        ships.name,
        ships.description,
        ships.image,
        users.username AS authorName,
        ships.authorId AS authorId,
        ships.timestamp
      FROM ships
      INNER JOIN users
      ON ships.authorId = users.id
      WHERE authorId = $userId
    SQL;

    try {
      $connection = $this->connect(); // Connect to the database
      $result = $connection->query($query)->fetch_all(MYSQLI_ASSOC); // Execute the query and fetch all results as associative array

      return $result; // Return the result
    } catch (Exception $e) {
      return []; // Error occured, return empty array
    } finally {
      $this->disconnect($connection); // Close the connection
    }
  }

  public function addShip(array $user, string $name, string $description, array $image): bool
  {
    // Query: Insert a new ship with the author's ID ?, the name ?, description ? and a nice image ?.
    $query = "INSERT INTO ships(authorId,name,description,image) VALUES (?,?,?,?)";

    $userId = $user["id"]; // Get the user ID from assoc
    $safeName = htmlspecialchars($name); // Escape the name to prevent XSS
    $safeDescription = htmlspecialchars($description); // Escape the description to prevent XSS
    $imageTemp = $image["tmp_name"]; // Get the temporary path of the uploaded image

    $imageContents = file_get_contents($imageTemp); // Read the file contents of the temporary path

    try {
      $connection = $this->connect(); // connect to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("issb", $userId, $safeName, $safeDescription, $null); // Bind the parameters, with the image being $null for send_long_data
      $statement->send_long_data(3, $imageContents); // Send the read image contents as parameter number 3 (4 if counted from 1) 
      $statement->execute(); // Execute the statement

      return true; // Success
    } catch (Exception $e) {
      return false; // Error occured, return false
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function deletes the specified ship
  public function deleteShip(int $shipId)
  {
    // Query: Delete all ships whose ID matches ?
    $query = "DELETE FROM ships WHERE id = ?";

    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("i", $shipId); // Bind the ship ID to ?
      $statement->execute(); // Execute the statement

      return true; // Sucess
    } catch (Exception $e) {
      return false; // Error occured, return false
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function gets a ship by its ID
  public function getShipById(int $shipId)
  {
    // Query: Get all ships where the ID is equal to $shipId, and add the username of each row's author to the row.
    $query = <<<SQL
      SELECT 
        ships.id,
        ships.name,
        ships.description,
        ships.image,
        users.username AS authorName,
        ships.authorId AS authorId,
        ships.timestamp
      FROM ships
      INNER JOIN users
      ON ships.authorId = users.id
      WHERE ships.id = $shipId
    SQL;

    try {
      $connection = $this->connect(); // Connect to the database
      $result = $connection->query($query)->fetch_all(MYSQLI_ASSOC); // Execute the query and fetch all results as associative array

      // 0 results? Ship not found, return empty array
      if (count($result) === 0)
        return [];

      // Return the first result. There should really only ever be one result because ID is a PRIMARY INDEX
      return $result[0];
    } catch (Exception $e) {
      return []; // Error occured, return empty array
    } finally {
      $this->disconnect($connection); // Close the connection
    }
  }
}

// Global instance of ShipStorage
$shipStorage = new ShipStorage();