<?php

class ShipStorage extends DatabaseModule
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getAllShips(): array
  {
    try {
      $connection = $this->connect();
      $query = <<<SQL
        SELECT 
          ships.id,
          ships.name,
          ships.description,
          ships.image,
          users.username AS authorName,
          ships.authorId,
          ships.timestamp
        FROM 
          ships
        INNER JOIN 
          users 
        ON 
          ships.authorId = users.id;
      SQL;
      $result = $connection->query($query)->fetch_all(MYSQLI_ASSOC);

      return $result;
    } catch (Exception $e) {
      return [];
    } finally {
      $this->disconnect($connection);
    }
  }

  public function getShipsOfUser(array $user)
  {
    $userId = $user["id"];
    try {
      $connection = $this->connect();
      $query = $query = <<<SQL
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

      $result = $connection->query($query)->fetch_all(MYSQLI_ASSOC);

      return $result;
    } catch (Exception $e) {
      return [];
    } finally {
      $this->disconnect($connection);
    }
  }

  public function addShip(array $user, string $name, string $description, array $image): bool
  {
    $userId = $user["id"];
    $safeName = htmlspecialchars($name);
    $safeDescription = htmlspecialchars($description);
    $imageTemp = $image["tmp_name"];

    $imageContents = file_get_contents($imageTemp);

    try {
      $connection = $this->connect();
      $query = "INSERT INTO ships(authorId,name,description,image) VALUES (?,?,?,?)";
      $statement = $connection->prepare($query);
      $statement->bind_param("issb", $userId, $safeName, $safeDescription, $null);
      $statement->send_long_data(3, $imageContents);
      $statement->execute();

      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
      // return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function deleteShip(int $shipId)
  {
    try {
      $connection = $this->connect();
      $query = "DELETE FROM ships WHERE id = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("i", $shipId);
      $statement->execute();

      return true;
    } catch (Exception $e) {
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function getShipById(int $shipId)
  {
    try {
      $connection = $this->connect();
      $query = $query = <<<SQL
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

      $result = $connection->query($query)->fetch_all(MYSQLI_ASSOC);

      if (count($result) === 0)
        return [];

      return $result[0];
    } catch (Exception $e) {
      return [];
    } finally {
      $this->disconnect($connection);
    }
  }
}

$shipStorage = new ShipStorage();