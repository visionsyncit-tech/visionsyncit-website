<?php
/* ============================================================
   VisionSync IT — Database Connection
   ============================================================ */

define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');       // ← غيّر هذا
define('DB_PASS', 'your_db_password');   // ← غيّر هذا
define('DB_NAME', 'visionsyncit_db');

function getDB(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    try {
      $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
        ]
      );
    } catch (PDOException $e) {
      http_response_code(500);
      die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
    }
  }
  return $pdo;
}