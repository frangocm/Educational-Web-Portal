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

/* ===== AUTO SEED (μία φορά μόνο) ===== */
$count = (int)$pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();

if ($count === 0) {
  $seed = $pdo->prepare("INSERT INTO announcements (`date`, subject, body) VALUES (?, ?, ?)");

  $seed->execute([
    '2025-10-01',
    'Ανακοίνωση 1',
    "Σας ενημερώνουμε ότι ξεκινά η διεξαγωγή του μαθήματος Εκπαιδευτικά Περιβάλλοντα Διαδικτύου.\n".
    "Κατά τη διάρκεια του εξαμήνου οι φοιτητές θα εξοικειωθούν με βασικές και προχωρημένες έννοιες της ανάπτυξης ιστοσελίδων.\n\n".
    "Παρακαλούνται όλοι οι φοιτητές να παρακολουθούν τακτικά τον ιστοχώρο του μαθήματος."
  ]);

  $seed->execute([
    '2025-12-30',
    'Ανακοίνωση 2',
    "Ανακοινώνεται η έναρξη των εργασιών.\n\n".
    "Η 1η εργασία έχει ανακοινωθεί στην ιστοσελίδα <a href=\"homework.php\">Εργασίες</a>."
  ]);
}

/* ===== ΦΘΙΝΟΥΣΑ ΣΕΙΡΑ ===== */
$stmt = $pdo->query("
  SELECT * 
  FROM announcements 
  ORDER BY `date` DESC, id DESC
");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isTutor = (isset($_SESSION['role']) && $_SESSION['role'] === 'tutor');

function h($s){
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Ανακοινώσεις</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Ανακοινώσεις</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($isTutor): ?>
          <div class="card" style="text-align:center;">
            <a class="btn" href="announcement_add.php">
              ➕ Προσθήκη νέας ανακοίνωσης
            </a>
          </div>
        <?php endif; ?>

        <?php 
          /* Ξεκινάμε από το πλήθος για σωστή αρίθμηση */
          $i = count($announcements); 
        ?>

        <?php foreach ($announcements as $ann): ?>
          <div class="card">

            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
              
              <h2 class="h2" style="margin:0;">
                Ανακοίνωση <?= $i ?>
              </h2>

              <?php if ($isTutor): ?>
                <div>
                  <a href="announcement_edit.php?id=<?= (int)$ann['id'] ?>">
                    [επεξεργασία]
                  </a>
                  &nbsp;
                  <a href="announcement_delete.php?id=<?= (int)$ann['id'] ?>"
                     onclick="return confirm('Σίγουρα θέλετε διαγραφή;')">
                    [διαγραφή]
                  </a>
                </div>
              <?php endif; ?>

            </div>

            <p class="p">
              <span class="bold">Ημερομηνία:</span>
              <?= h($ann['date']) ?>
            </p>

            <p class="p">
              <?= nl2br(strip_tags($ann['body'], '<a>')) ?>
            </p>

          </div>

          <?php $i--; ?>
        <?php endforeach; ?>

      </section>
    </div>

    <div class="top-link">
      <a href="#top">TOP</a>
    </div>

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