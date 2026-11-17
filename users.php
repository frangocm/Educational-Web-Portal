<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

if ($_SESSION['role'] !== 'tutor') {
  http_response_code(403);
  die('Access denied');
}

$users = $pdo->query("
  SELECT id, firstname, lastname, email, role
  FROM users
  ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Διαχείριση χρηστών</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Διαχείριση χρηστών</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

      <?php include 'menu.php'; ?>

      <section class="content">

        <div class="card" style="text-align:center;">
          <a class="btn" href="user_add.php">➕ Προσθήκη χρήστη</a>
        </div>

        <?php foreach ($users as $u): ?>
  <div class="card user-card">

    <div class="user-head">
      <div>
        <p class="user-title">
          <?= htmlspecialchars($u['email']) ?>
          <span class="user-badge <?= ($u['role']==='tutor' ? 'tutor' : '') ?>">
            <?= htmlspecialchars($u['role']) ?>
          </span>
        </p>
      </div>

      <div class="user-actions">
        <a href="user_edit.php?id=<?= (int)$u['id'] ?>">Επεξεργασία</a>
        <a href="user_delete.php?id=<?= (int)$u['id'] ?>"
           onclick="return confirm('Σίγουρα διαγραφή;')">Διαγραφή</a>
      </div>
    </div>

    <div class="user-meta">
      <div class="k">ID</div><div><?= (int)$u['id'] ?></div>
      <div class="k">Πεδίο login</div><div>email</div>
      <div class="k">Email</div><div><?= htmlspecialchars($u['email']) ?></div>
    </div>

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