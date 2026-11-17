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


$count = (int)$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
if ($count === 0) {
  $seed = $pdo->prepare(
    "INSERT INTO documents (title, description, filepath) VALUES (?, ?, ?)"
  );

  $seed->execute([
    'Οδηγός χρήσης XAMPP και Notepad++',
    'Το παρόν έγγραφο αποτελεί έναν αναλυτικό οδηγό εγκατάστασης και βασικής χρήσης των εργαλείων XAMPP και Notepad++, τα οποία είναι απαραίτητα για την ανάπτυξη και εκτέλεση ιστοσελίδων και web εφαρμογών σε τοπικό περιβάλλον. Απευθύνεται σε φοιτητές που έρχονται για πρώτη φορά σε επαφή με τεχνολογίες web.',
    'Οδηγός χρησιμοποίησης XAMPP και Notepad++.doc'
  ]);

  $seed->execute([
    'Εγχειρίδιο HTML',
    'Το εγχειρίδιο HTML περιλαμβάνει τις βασικές έννοιες της γλώσσας σήμανσης HTML, παραδείγματα σύνταξης και επεξηγήσεις των πιο συχνά χρησιμοποιούμενων ετικετών. Στόχος του εγγράφου είναι να βοηθήσει τους φοιτητές στην κατανόηση της δομής και της δημιουργίας ιστοσελίδων.',
    'ΕΓΧΕΙΡΙΔΙΟ HTML .doc'
  ]);
}


$stmt = $pdo->query(
  "SELECT id, title, description, filepath FROM documents ORDER BY id DESC"
);
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h($s){
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Έγγραφα μαθήματος</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Έγγραφα μαθήματος</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

    <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($isTutor): ?>
          <div class="card" style="text-align:center;">
            <a class="btn" href="document_add.php">
              ➕ Προσθήκη νέου εγγράφου
            </a>
          </div>
        <?php endif; ?>

        <?php foreach ($docs as $d): ?>
          <div class="card">

            
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <h2 class="h2" style="margin-top:0;">
                <?= h($d['title']) ?>
              </h2>

              <?php if ($isTutor): ?>
                <div>
                  <a href="document_edit.php?id=<?= (int)$d['id'] ?>">[επεξεργασία]</a>
                  &nbsp;
                  <a href="document_delete.php?id=<?= (int)$d['id'] ?>"
                     onclick="return confirm('Σίγουρα διαγραφή;')">
                    [διαγραφή]
                  </a>
                </div>
              <?php endif; ?>
            </div>

            <p class="p">
              <span class="bold">Περιγραφή:</span>
              <?= nl2br(h($d['description'])) ?>
            </p>

            <div class="tools">
              <a class="tool-link"
                 href="files/<?= rawurlencode($d['filepath']) ?>"
                 target="_blank">
                Download
              </a>
            </div>

          </div>
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