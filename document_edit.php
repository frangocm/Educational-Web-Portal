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

function h($s){ return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }

function safe_filename($name){
  $name = trim((string)$name);
  if ($name === '') return '';
  
  $name = preg_replace('/[^\p{L}\p{N}\.\_\-\s]/u', '', $name);
  $name = preg_replace('/\s+/', ' ', $name);
  return trim($name);
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: documents.php'); exit; }


$st = $pdo->prepare("SELECT id, title, description, filepath FROM documents WHERE id=?");
$st->execute([$id]);
$doc = $st->fetch(PDO::FETCH_ASSOC);
if (!$doc) { header('Location: documents.php'); exit; }

$error = '';
$title = $doc['title'];
$description = $doc['description'];
$filepath = $doc['filepath']; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');

  if ($title === '' || $description === '') {
    $error = 'Συμπλήρωσε τίτλο και περιγραφή.';
  } else {

    
    if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
      if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Σφάλμα στο ανέβασμα αρχείου.';
      } else {
        $uploadDir = __DIR__ . '/files/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

        $orig = $_FILES['file']['name'] ?? '';
        $tmp  = $_FILES['file']['tmp_name'] ?? '';

        $safe = safe_filename($orig);
        if ($safe === '') $safe = 'document';

        $final = $safe;
        $target = $uploadDir . $final;

        if (file_exists($target)) {
          $final = time() . '_' . $safe;
          $target = $uploadDir . $final;
        }

        if (!move_uploaded_file($tmp, $target)) {
          $error = 'Δεν μπόρεσε να αποθηκευτεί το αρχείο στον φάκελο files/.';
        } else {
         
          if (!empty($filepath)) {
            $oldPath = __DIR__ . '/files/' . $filepath;
            if (file_exists($oldPath)) @unlink($oldPath);
          }
          $filepath = $final;
        }
      }
    }

    if ($error === '') {
      $upd = $pdo->prepare("UPDATE documents SET title=?, description=?, filepath=? WHERE id=?");
      $upd->execute([$title, $description, $filepath, $id]);

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
  <title>Επεξεργασία εγγράφου</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Επεξεργασία εγγράφου</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($error): ?>
          <div class="card" style="border-left:6px solid rgba(180,35,24,0.70);">
            <p class="p" style="margin:0; font-weight:800; color:#b42318;"><?= h($error) ?></p>
          </div>
        <?php endif; ?>

        <div class="card">
          <h2 class="h2" style="margin-top:0;">Διόρθωση στοιχείων εγγράφου</h2>

          <div class="formbox" style="margin: 10px auto 0;">
            <form method="post" enctype="multipart/form-data">

              <label class="label">Τίτλος</label>
              <input class="input" type="text" name="title" value="<?= h($title) ?>" required>

              <label class="label">Περιγραφή</label>
              <textarea class="input" name="description" rows="7" required><?= h($description) ?></textarea>

              <label class="label">Νέο αρχείο (προαιρετικό)</label>
              <input class="input" type="file" name="file">

              <?php if (!empty($filepath)): ?>
                <div class="tools" style="margin-top:10px;">
                  <span class="tool-link" style="opacity:.7;">Τρέχον:</span>
                  <a class="tool-link" href="files/<?= rawurlencode($filepath) ?>" target="_blank" rel="noopener">
                    <?= h($filepath) ?>
                  </a>
                </div>
              <?php endif; ?>

              <button class="btn" type="submit">Αποθήκευση</button>
              <a class="tool-link" href="documents.php" style="margin-left:10px;">Ακύρωση</a>

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
        <div class="footer-right">© 2026 • Όλα τα δικαιώματα διατηρούνται</div>
      </div>
    </div>

  </div>
</div>

</body>
</html>