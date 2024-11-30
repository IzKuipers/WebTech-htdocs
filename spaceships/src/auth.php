<?php

require_once "env.php";
require_once "error.php";

use Ramsey\Uuid\Uuid;

class AuthorizationManager
{
  private string $hostname;
  private string $username;
  private string $password;
  private string $database;

  public function __construct()
  {
    $this->hostname = DB_HOSTNAME;
    $this->username = DB_USERNAME;
    $this->password = DB_PASSWORD;
    $this->database = DB_DATABASE;
  }

  ////////////////
  // CONNECTION //
  ////////////////

  public function connect(): mysqli
  {
    $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);

    if ($connection->connect_error) {
      throw new Exception("Failed to connect to database: " . $connection->connect_error);
    }

    return $connection;
  }

  public function disconnect(mysqli $connection, \mysqli_stmt $statement = null)
  {
    if (isset($statement) && $statement instanceof mysqli_stmt) {
      try {
        $statement->close();
      } catch (Exception $e) {
        $e->getMessage();
        // silently error
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
      $connection = $this->connect();
      $query = "INSERT INTO tokens(value,userId) VALUES (?,?)";
      $statement = $connection->prepare($query);

      $statement->bind_param("si", $token, $user["id"]);
      $statement->execute();

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
      $connection = $this->connect();
      $query = "SELECT userId,id FROM tokens WHERE value = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $token);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0) {
        return false;
      }

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

      if ($result->num_rows == 0) {
        Dialog(ErrorMessages::UserNotFound);

        return "";
      }

      $row = $result->fetch_assoc();
      $hash = $row["hash"];

      $passwordValid = $this->verifyPassword($hash, $password);

      if (!$passwordValid) {
        Dialog(ErrorMessages::PasswordIncorrect);

        return "";
      }

      $token = $this->createToken($row);

      return $token;
    } catch (Exception $e) {
      return "";
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
      return true;
    } catch (Exception $e) {
      Dialog(ErrorMessages::UserAlreadyExists);
      return false;
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

  /////////////
  // GETTING //
  /////////////

  public function getUserByToken(string $token)
  {
    try {
      $connection = $this->connect();
      $query = "SELECT userId FROM tokens WHERE value = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $token);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0)
        return null;

      $row = $result->fetch_assoc();
      $userId = (int) $row["userId"];

      return $this->getUserById($userId);
    } catch (Exception $e) {
      return null;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function getUserById(int $userId)
  {
    try {
      $connection = $this->connect();
      $query = "SELECT * FROM users WHERE id = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("i", $userId);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0)
        return null;

      $row = $result->fetch_assoc();

      return $row;
    } catch (Exception $e) {
      return null;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }


  public function getUserByName(string $username)
  {
    try {
      $connection = $this->connect();
      $query = "SELECT * FROM users WHERE username = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $username);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0)
        return null;

      $row = $result->fetch_assoc();

      return $row;
    } catch (Exception $e) {
      return null;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }
}

$authManager = new AuthorizationManager();