<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') {
  header('Location: index.php');
  exit;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: users.php'); exit; }

$stmt = $pdo->prepare("SELECT id, firstname, lastname, email, role FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { die('Δεν βρέθηκε χρήστης.'); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstname = trim($_POST['firstname'] ?? '');
  $lastname  = trim($_POST['lastname'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $role      = trim($_POST['role'] ?? 'student');
  $newPass   = trim($_POST['password'] ?? '');

  if ($firstname === '' || $lastname === '' || $email === '') {
    $error = 'Συμπλήρωσε όνομα, επώνυμο και email.';
  } else {

    // μην αφήσεις να "ρίξει" τον εαυτό του από tutor
    if ($id === (int)$_SESSION['user_id'] && $role !== 'tutor') {
      $error = 'Δεν μπορείς να αφαιρέσεις τον tutor ρόλο από τον εαυτό σου.';
    } else {

      // έλεγχος μοναδικότητας email (εκτός του ίδιου id)
      $chk = $pdo->prepare("SELECT id FROM users WHERE email=? AND id<>? LIMIT 1");
      $chk->execute([$email, $id]);
      if ($chk->fetchColumn()) {
        $error = 'Υπάρχει ήδη χρήστης με αυτό το email.';
      } else {

        if ($newPass !== '') {
          // ΣΗΜΑΝΤΙΚΟ: κρατάμε ΙΔΙΑ λογική με login.php (SHA-256)
          $hash = hash('sha256', $newPass);
          $upd = $pdo->prepare("UPDATE users SET firstname=?, lastname=?, email=?, role=?, password=? WHERE id=?");
          $upd->execute([$firstname, $lastname, $email, $role, $hash, $id]);
        } else {
          $upd = $pdo->prepare("UPDATE users SET firstname=?, lastname=?, email=?, role=? WHERE id=?");
          $upd->execute([$firstname, $lastname, $email, $role, $id]);
        }

        header('Location: users.php');
        exit;
      }
    }
  }

  // κράτα τις τιμές στη φόρμα αν έχει error
  $user['firstname'] = $firstname;
  $user['lastname']  = $lastname;
  $user['email']     = $email;
  $user['role']      = $role;
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Επεξεργασία χρήστη</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Επεξεργασία χρήστη</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($error): ?>
          <p class="error"><?= h($error) ?></p>
        <?php endif; ?>

        <div class="formbox">
          <form method="post">

            <label class="label">Όνομα</label>
            <input class="input" type="text" name="firstname" value="<?= h($user['firstname']) ?>" required>

            <label class="label">Επώνυμο</label>
            <input class="input" type="text" name="lastname" value="<?= h($user['lastname']) ?>" required>

            <label class="label">Email</label>
            <input class="input" type="email" name="email" value="<?= h($user['email']) ?>" required>

            <label class="label">Νέος κωδικός (άφησέ το κενό αν δεν αλλάζει)</label>
            <input class="input" type="password" name="password" value="">

            <label class="label">Ρόλος</label>
            <select class="input" name="role">
              <option value="student" <?= ($user['role']==='student'?'selected':'') ?>>Student</option>
              <option value="tutor"   <?= ($user['role']==='tutor'?'selected':'') ?>>Tutor</option>
            </select>

            <button class="btn" type="submit">Αποθήκευση</button>

          </form>
        </div>

        <div class="card" style="margin-top:16px;">
          <a class="tool-link" href="users.php">← Επιστροφή</a>
        </div>

      </section>
    </div>

    <!-- TOP -->
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