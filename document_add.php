<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') { http_response_code(403); die('Access denied'); }

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$error = '';
$title = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim((string)($_POST['title'] ?? ''));
  $description = trim((string)($_POST['description'] ?? ''));

  if ($title === '' || $description === '') {
    $error = 'Συμπλήρωσε τίτλο και περιγραφή.';
  } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $error = 'Διάλεξε αρχείο.';
  } else {
    $uploadDir = __DIR__ . '/files/';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }

    $original = basename($_FILES['file']['name'] ?? '');
    $tmp = $_FILES['file']['tmp_name'];

    
    $safe = preg_replace('/[^A-Za-z0-9.\-_ ]/', '', $original);
    $safe = trim($safe);
    if ($safe === '') { $safe = 'document'; }

    $finalName = $safe;
    $target = $uploadDir . $finalName;

    if (file_exists($target)) {
      $finalName = time() . '_' . $safe;
      $target = $uploadDir . $finalName;
    }

    if (!move_uploaded_file($tmp, $target)) {
      $error = 'Δεν μπόρεσε να αποθηκευτεί το αρχείο στον φάκελο files/.';
    } else {
      
      $ins = $pdo->prepare("INSERT INTO documents (title, description, filepath) VALUES (?, ?, ?)");
      $ins->execute([$title, $description, $finalName]);

      header('Location: documents.php');
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Προσθήκη εγγράφου</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Προσθήκη εγγράφου</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">
      <aside class="sidebar">
        <nav class="nav">
          <a class="navbtn active" href="documents.php">Έγγραφα μαθήματος</a>
          <a class="navbtn" href="logout.php">Logout</a>
        </nav>
      </aside>

      <section class="content">

        <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>

        <div class="formbox">
          <form method="post" enctype="multipart/form-data">
            <label class="label">Τίτλος</label>
            <input class="input" type="text" name="title" value="<?= h($title) ?>" required>

            <label class="label">Περιγραφή</label>
            <textarea class="input" name="description" rows="6" required><?= h($description) ?></textarea>

            <label class="label">Αρχείο</label>
            <input class="input" type="file" name="file" required>

            <button class="btn" type="submit">Αποθήκευση</button>
          </form>
        </div>

        <div class="card" style="margin-top:16px;">
          <a class="tool-link" href="documents.php">← Επιστροφή</a>
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
        <div class="footer-right">© 2026 • Όλα τα δικαιώματα διατηρούνται</div>
      </div>
    </div>

  </div>
</div>

</body>
</html>