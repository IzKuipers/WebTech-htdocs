<?php

include_once __DIR__ . "/../../env.php";


class DatabaseModule
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
}