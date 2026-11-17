<?php
// Copy this file to config.php and enter your local database settings.
$host = 'localhost';
$port = '3306';
$db   = 'educational_web_portal';
$user = 'your_database_user';
$pass = 'your_database_password';

$pdo = new PDO(
  "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
  $user,
  $pass,
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]
);
