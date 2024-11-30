<?php

include_once __DIR__ . "/../../env.php";

/**
 * DATABASE MODULE
 * 
 * This base class contains basic functions for connecting to and disconnecting
 * from the database. The credentials are pulled from the env.php file.
 */

class DatabaseModule
{
  private string $hostname; // SQL hostname
  private string $username; // Username
  private string $password; // Password
  private string $database; // Database

  public function __construct()
  {
    $this->hostname = DB_HOSTNAME;
    $this->username = DB_USERNAME;
    $this->password = DB_PASSWORD;
    $this->database = DB_DATABASE;
  }

  // This function returns a mysqli instance, connected using the credentials
  // stored in the class instance. It throws an error if connection failed.
  public function connect(): mysqli
  {
    // Create mysqli class instance
    $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);

    // Connection error? Throw it.
    if ($connection->connect_error) {
      throw new Exception((string) "Failed to connect to database: " . $connection->connect_error);
    }

    // Return the class instance
    return $connection;
  }

  // This function closes a provided mysqli instand and optionally a mysqli statement
  public function disconnect(mysqli $connection, mysqli_stmt $statement = null)
  {
    // Is there a statement?
    if (isset($statement) && $statement instanceof mysqli_stmt) {
      try {
        $statement->close(); // Close it.
      } catch (Exception $e) {
        // silently error
      }
    }

    $connection->close(); // Close the database connection
  }
}