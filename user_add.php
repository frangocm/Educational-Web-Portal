<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';

/* Μόνο tutor */
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') {
  header('Location: index.php');
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $firstname = trim($_POST['firstname'] ?? '');
  $lastname  = trim($_POST['lastname'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $password  = trim($_POST['password'] ?? '');
  $role      = trim($_POST['role'] ?? 'student');

  if ($firstname === '' || $lastname === '' || $email === '' || $password === '') {
    $error = 'Συμπλήρωσε όλα τα πεδία.';
  } else {

    /* Έλεγχος αν υπάρχει ήδη email */
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->execute([$email]);

    if ($check->fetchColumn()) {
      $error = 'Υπάρχει ήδη χρήστης με αυτό το email.';
    } else {

      /* SHA-256 (όπως στο login σου) */
      $hash = hash('sha256', $password);

      $stmt = $pdo->prepare("
        INSERT INTO users (firstname, lastname, email, password, role)
        VALUES (?, ?, ?, ?, ?)
      ");

      $stmt->execute([$firstname, $lastname, $email, $hash, $role]);

      header('Location: users.php');
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Προσθήκη χρήστη</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Προσθήκη χρήστη</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if (!empty($error)): ?>
          <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="formbox">
          <form method="post">

            <label class="label">Όνομα</label>
            <input class="input" type="text" name="firstname" required>

            <label class="label">Επώνυμο</label>
            <input class="input" type="text" name="lastname" required>

            <label class="label">Email</label>
            <input class="input" type="email" name="email" required>

            <label class="label">Κωδικός</label>
            <input class="input" type="password" name="password" required>

            <label class="label">Ρόλος</label>
            <select class="input" name="role">
              <option value="student">Student</option>
              <option value="tutor">Tutor</option>
            </select>

            <button class="btn" type="submit">Αποθήκευση</button>

          </form>
        </div>

      </section>
    </div>

    <!-- TOP -->
    <div class="top-link">
      <a href="#top">TOP</a>
    </div>

    <!-- FOOTER -->
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