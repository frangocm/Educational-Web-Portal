<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') { http_response_code(403); die('Access denied'); }

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $del = $pdo->prepare("DELETE FROM assignments WHERE id=?");
  $del->execute([$id]);
}

header('Location: homework.php');
exit;