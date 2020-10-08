<?php

class DbConnection {
  static private $conn = null;

  static public function get() {
    if (self::$conn) {
      return self::$conn;
    }

    self::$conn = new PDO("mysql:dbname=mytube;host=localhost", "root", "");
    self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    return self::$conn;
  }
}

// $g_dbConn = null;
// try {
//   $g_dbConn = new PDO("mysql:dbname=mytube;host=localhost", "root", "");
//   $g_dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
// } catch (PDOException $e) {
//   Response::fail("Connection failed: " . $e->getMessage());
// }
