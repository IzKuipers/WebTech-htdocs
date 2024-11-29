<?php

require_once "../vendor/autoload.php";

use Ramsey\Uuid\Uuid;

class AuthorizationManager
{
  private string $hostname;
  private string $username;
  private string $password;
  private string $database;

  public function __construct(string $hostname, string $username, string $password, string $database)
  {
    $this->hostname = $hostname;
    $this->username = $username;
    $this->password = $password;
    $this->database = $database;

    $this->initializeDatabase();
  }

  //////////////
  // DATABASE //
  //////////////

  public function initializeDatabase()
  {
    try {
      global $connection;

      $connection = $this->connect();
      $query = <<<SQL
        SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
        START TRANSACTION;
        SET time_zone = "+00:00";
  
  
        /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
        /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
        /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
        /*!40101 SET NAMES utf8mb4 */;
  
        CREATE DATABASE IF NOT EXISTS `spaceships` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
        USE `spaceships`;
  
        CREATE TABLE IF NOT EXISTS `tokens` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `userId` int(11) NOT NULL,
          `value` varchar(64) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `userId` (`userId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  
        CREATE TABLE IF NOT EXISTS `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(64) NOT NULL,
          `hash` varchar(512) NOT NULL,
          `savedata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`savedata`)),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  
        ALTER TABLE `tokens`
          ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        COMMIT;
  
        /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
        /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
        /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
      SQL;

      $connection->query($query);
    } catch (Exception $e) {
      throw new Exception("Failed to initialize database: " . $e->getMessage());
    } finally {
      $this->disconnect($connection);

    }
  }

  public function connect(): mysqli
  {
    $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);

    if ($connection->connect_error) {
      throw new Exception("Failed to connect to database: " . $connection->connect_error);
    }

    return $connection;
  }

  public function disconnect(mysqli $connection, mysqli_stmt $statement = null)
  {
    if (isset($statement)) {
      try {
        $statement->close();
      } catch (Exception $e) {
        printf("Failed to close a statement: " . $e->getMessage());
      }
    }

    $connection->close();
  }

  ////////////
  // TOKENS //
  ////////////

  public function createToken(array $user)
  {
    $token = Uuid::uuid4()->toString();

    try {
      global $connection, $statement;

      $connection = $this->connect();
      $query = "INSERT INTO tokens(value,userId) VALUES (?,?)";
      $statement = $connection->prepare($query);

      $statement->bind_param("si", $token, $user["id"]);
      $statement->execute();

      $this->disconnect($connection, $statement);

      return $token;
    } catch (Exception $e) {
      return "";
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function validateToken(string $token): bool
  {
    try {
      global $connection, $statement;

      $connection = $this->connect();
      $query = "SELECT userId,id FROM tokens WHERE value = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $token);
      $statement->execute();

      return true;
    } catch (Exception $e) {
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function discontinueToken(string $token): bool
  {
    try {
      global $connection, $statement;

      $connection = $this->connect();
      $query = "DELETE FROM tokens WHERE value = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $token);
      $statement->execute();

      return true;
    } catch (Exception $e) {
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  ////////////////////
  // AUTHENTICATION //
  ////////////////////

  public function authenticateUser(string $username, string $password): string
  {
    try {
      $connection = $this->connect();
      $query = "SELECT username,hash,id FROM users WHERE username = ?";
      $statement = $connection->prepare($query);

      $statement->bind_param("s", $username);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0)
        throw new Exception("No such user '$username'");

      $row = $result->fetch_assoc();
      $hash = $row["hash"];

      $passwordValid = $this->verifyPassword($hash, $password);

      if (!$passwordValid)
        throw new Exception("Access denied for '$username'");

      $token = $this->createToken($row);

      if (strlen($token) == 0)
        throw new Exception("Failed to generate token");

      return $token;
    } catch (Exception $e) {
      throw new Exception("Authentication failed: " . $e->getMessage());
    }
  }


  public function verifyPassword(string $hash, string $password): bool
  {
    return password_verify($password, $hash);
  }

  ///////////////////
  // USER MUTATION //
  ///////////////////

  public function createUser($username, $password)
  {
    $safeUsername = htmlspecialchars(trim($username));
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users(username,hash) VALUES (?,?)";

    try {
      global $connection, $statement;
      $connection = $this->connect();
      $statement = $connection->prepare($query);
      $statement->bind_param("ss", $safeUsername, $hash);
      $statement->execute();
    } catch (Exception $e) {
      throw new Exception("Failed to create user: " . $e->getMessage());
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function deleteUser(string $username)
  {
    try {
      global $connection, $statement;
      $connection = $this->connect();
      $query = "DELETE FROM users WHERE username = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $username);
      $statement->execute();

      return true;
    } catch (Exception $e) {
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }
}