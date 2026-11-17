<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
  header('Location: announcements.php');
  exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
  $stmt = $pdo->prepare("DELETE FROM announcements WHERE id=?");
  $stmt->execute([$id]);
}

header('Location: announcements.php');
exit;