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

$isTutor = (isset($_SESSION['role']) && strtolower(trim((string)$_SESSION['role'])) === 'tutor');
if (!$isTutor) {
  header('Location: documents.php');
  exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
  
  $st = $pdo->prepare("SELECT filepath FROM documents WHERE id=?");
  $st->execute([$id]);
  $file = $st->fetchColumn();

  if ($file) {
    $path = __DIR__ . '/files/' . $file;
    if (file_exists($path)) {
      @unlink($path);
    }
  }

  $del = $pdo->prepare("DELETE FROM documents WHERE id=?");
  $del->execute([$id]);
}

header('Location: documents.php?msg=deleted');
exit;