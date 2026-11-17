<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') {
  header('Location: index.php');
  exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: users.php'); exit; }


if ($id === (int)$_SESSION['user_id']) {
  header('Location: users.php?err=selfdelete');
  exit;
}


$cntTutor = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='tutor'")->fetchColumn();
$roleStmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
$roleStmt->execute([$id]);
$role = $roleStmt->fetchColumn();

if ($role === 'tutor' && $cntTutor <= 1) {
  header('Location: users.php?err=lasttutor');
  exit;
}

$del = $pdo->prepare("DELETE FROM users WHERE id=?");
$del->execute([$id]);

header('Location: users.php');
exit;
