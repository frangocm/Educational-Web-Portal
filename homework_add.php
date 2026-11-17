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
$goals = '';
$deliverables = '';
$due_date = '';
$description_file = '';

function safe_filename($name){
  $name = trim((string)$name);
  if ($name === '') return '';
  
  $name = preg_replace('/[^\p{L}\p{N}\.\_\-\s]/u', '', $name);
  $name = preg_replace('/\s+/', ' ', $name);
  return trim($name);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $goals = trim($_POST['goals'] ?? '');
  $deliverables = trim($_POST['deliverables'] ?? '');
  $due_date = trim($_POST['due_date'] ?? '');

  if ($goals === '' || $deliverables === '' || $due_date === '') {
    $error = 'Συμπλήρωσε Στόχους, Παραδοτέα και Ημερομηνία παράδοσης.';
  } else {

    
    if (isset($_FILES['description_file']) && $_FILES['description_file']['error'] !== UPLOAD_ERR_NO_FILE) {
      if ($_FILES['description_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Σφάλμα στο ανέβασμα αρχείου.';
      } else {
        $uploadDir = __DIR__ . '/files/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

        $orig = $_FILES['description_file']['name'] ?? '';
        $tmp  = $_FILES['description_file']['tmp_name'] ?? '';

        $safe = safe_filename($orig);
        if ($safe === '') $safe = 'assignment_file';

        $final = $safe;
        $target = $uploadDir . $final;

        if (file_exists($target)) {
          $final = time() . '_' . $safe;
          $target = $uploadDir . $final;
        }

        if (!move_uploaded_file($tmp, $target)) {
          $error = 'Δεν μπόρεσε να αποθηκευτεί το αρχείο στον φάκελο files/.';
        } else {
          $description_file = $final;
        }
      }
    }

    if ($error === '') {

      
      $ins = $pdo->prepare("
        INSERT INTO assignments (goals, description_file, deliverables, due_date)
        VALUES (?, ?, ?, ?)
      ");
      $ins->execute([$goals, $description_file, $deliverables, $due_date]);

      
      $N = (int)$pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();

      
      $today = date('Y-m-d');
      $ann = $pdo->prepare("INSERT INTO announcements (`date`, subject, body) VALUES (?, ?, ?)");
      $ann->execute([
        $today,
        "Υποβλήθηκε η εργασία $N",
        "Η ημερομηνία παράδοσης της εργασίας είναι $due_date"
      ]);

      header('Location: homework.php');
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Προσθήκη εργασίας</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Προσθήκη εργασίας</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">
      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($error): ?>
          <p class="error"><?= h($error) ?></p>
        <?php endif; ?>

        <div class="formbox" style="margin: 10px auto 0;">
          <form method="post" enctype="multipart/form-data">

            <label class="label">Στόχοι</label>
            <textarea class="input" name="goals" rows="6" required><?= h($goals) ?></textarea>

            <label class="label">Παραδοτέα</label>
            <textarea class="input" name="deliverables" rows="5" required><?= h($deliverables) ?></textarea>

            <label class="label">Ημερομηνία παράδοσης</label>
            <input class="input" type="date" name="due_date" value="<?= h($due_date) ?>" required>

            <label class="label">Αρχείο εκφώνησης (προαιρετικό)</label>
            <input class="input" type="file" name="description_file">

            <button class="btn" type="submit">Αποθήκευση</button>

          </form>
        </div>

        <div class="card" style="margin-top:16px;">
          <a class="tool-link" href="homework.php">← Επιστροφή</a>
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