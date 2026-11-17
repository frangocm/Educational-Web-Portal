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

function h($s){
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  header('Location: announcements.php');
  exit;
}

/* Φέρε ανακοίνωση */
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE id=?");
$stmt->execute([$id]);
$ann = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ann) {
  header('Location: announcements.php');
  exit;
}

$error = '';
$date = $ann['date'] ?? '';
$subject = $ann['subject'] ?? '';
$body = $ann['body'] ?? '';

/* Update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $date = trim($_POST['date'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $body = trim($_POST['body'] ?? '');

  if ($date === '' || $subject === '' || $body === '') {
    $error = 'Συμπλήρωσε όλα τα πεδία.';
  } else {
    $up = $pdo->prepare("UPDATE announcements SET `date`=?, subject=?, body=? WHERE id=?");
    $up->execute([$date, $subject, $body, $id]);

    header('Location: announcements.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Επεξεργασία ανακοίνωσης</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Επεξεργασία ανακοίνωσης</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($error): ?>
          <div class="card" style="border-left:6px solid rgba(180,35,24,0.70);">
            <p class="p" style="margin:0; font-weight:800; color:#b42318;">
              <?= h($error) ?>
            </p>
          </div>
        <?php endif; ?>

        <div class="card">
          <h2 class="h2" style="margin-top:0;">Διόρθωση στοιχείων ανακοίνωσης</h2>

          <div class="formbox" style="margin: 10px auto 0;">
            <form method="post">

              <label class="label">Ημερομηνία</label>
              <input class="input" type="date" name="date" value="<?= h($date) ?>" required>

              <label class="label">Θέμα</label>
              <input class="input" type="text" name="subject" value="<?= h($subject) ?>" required>

              <label class="label">Κείμενο</label>
              <textarea class="input" name="body" rows="8" required><?= h($body) ?></textarea>

              <button class="btn" type="submit">Αποθήκευση αλλαγών</button>
              <a class="tool-link" href="announcements.php" style="margin-left:10px;">Ακύρωση</a>

            </form>
          </div>
        </div>

      </section>
    </div>

    <div class="top-link"><a href="#top">TOP</a></div>

    <div class="footer">
      <div class="footer-inner">
        <div class="footer-left">
          <strong>Εκπαιδευτικά Περιβάλλοντα Διαδικτύου</strong><br>
          Τμήμα Πληροφορικής – Πανεπιστήμιο
        </div>
        <div class="footer-right">
          © 2026 • Όλα τα δικαιώματα διατηρούνται
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>
