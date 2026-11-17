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

$sender  = trim($_POST['sender'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($sender === '' || $subject === '' || $message === '') {
  header('Location: communication.php?sent=0&reason=missing');
  exit;
}

if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
  header('Location: communication.php?sent=0&reason=bad_email');
  exit;
}


$st = $pdo->prepare("SELECT id, email FROM users WHERE email = ? LIMIT 1");
$st->execute([$sender]);
$user = $st->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  header('Location: communication.php?sent=0&reason=sender_not_found');
  exit;
}


$sessionEmail = (string)($_SESSION['email'] ?? '');
if ($sessionEmail !== '' && strcasecmp($sessionEmail, $sender) !== 0) {
  header('Location: communication.php?sent=0&reason=sender_mismatch');
  exit;
}


$stmt = $pdo->prepare("SELECT email FROM users WHERE role='tutor' ORDER BY email");
$stmt->execute();
$tutors = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$tutors || count($tutors) === 0) {
  header('Location: communication.php?sent=0&reason=notutors');
  exit;
}


$recipientsStr = implode(', ', $tutors);

$ins = $pdo->prepare("
  INSERT INTO mail_log (sender, subject, body, recipients, status)
  VALUES (?, ?, ?, ?, ?)
");
$ins->execute([$sender, $subject, $message, $recipientsStr, 'logged']);

$mailId = $pdo->lastInsertId();

header('Location: communication.php?sent=1&mail_id=' . urlencode((string)$mailId));
exit;