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

$isTutor = (isset($_SESSION['role']) && $_SESSION['role'] === 'tutor');

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

/*
  ΘΕΛΟΥΜΕ:
  - display_no = 1..N με βάση τη σειρά που "μετράμε" (παλιά -> νέα)
  - αλλά εμφάνιση (πάνω -> κάτω): νέα -> παλιά
*/
$stmt = $pdo->query("
  SELECT * FROM (
    SELECT
      a.*,
      ROW_NUMBER() OVER (ORDER BY a.id ASC) AS display_no
    FROM assignments a
  ) t
  ORDER BY t.id DESC
");
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Εργασίες</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Εργασίες</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">
      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($isTutor): ?>
          <div class="card" style="text-align:center;">
            <a class="btn" href="homework_add.php">➕ Προσθήκη νέας εργασίας</a>
          </div>
        <?php endif; ?>

        <?php foreach ($assignments as $a): ?>
          <div class="card">

            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
              <h2 class="h2" style="margin-top:0;">
                Εργασία <?= (int)$a['display_no'] ?>
              </h2>

              <?php if ($isTutor): ?>
                <div>
                  <a href="homework_edit.php?id=<?= (int)$a['id'] ?>">[επεξεργασία]</a>
                  &nbsp;
                  <a href="homework_delete.php?id=<?= (int)$a['id'] ?>"
                     onclick="return confirm('Σίγουρα διαγραφή;')">[διαγραφή]</a>
                </div>
              <?php endif; ?>
            </div>

            <p class="p"><span class="bold">Στόχοι:</span></p>
            <div class="p"><?= nl2br(h($a['goals'])) ?></div>

            <p class="p" style="margin-top:14px;"><span class="bold">Εκφώνηση:</span></p>
            <div class="tools">
              <?php if (!empty($a['description_file'])): ?>
                <a class="tool-link"
                   href="files/<?= rawurlencode($a['description_file']) ?>"
                   target="_blank">Κατεβάστε την εκφώνηση (εδώ)</a>
              <?php else: ?>
                <span class="tool-link" style="opacity:.6;">(δεν υπάρχει αρχείο)</span>
              <?php endif; ?>
            </div>

            <p class="p" style="margin-top:14px;"><span class="bold">Παραδοτέα:</span></p>
            <div class="p"><?= nl2br(h($a['deliverables'])) ?></div>

            <p class="p deadline" style="margin-top:14px;">
              <span class="bold">Ημερομηνία παράδοσης:</span> <?= h($a['due_date']) ?>
            </p>

          </div>
        <?php endforeach; ?>

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
