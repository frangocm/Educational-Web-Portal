<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    $error = 'Συμπληρώστε e-mail και κωδικό.';
  } else {
    $st = $pdo->prepare(
      "SELECT id, email, password, role FROM users WHERE email = ? LIMIT 1"
    );
    $st->execute([$email]);
    $u = $st->fetch(PDO::FETCH_ASSOC);

    // Ο κωδικός στη βάση είναι SHA-256
    if ($u && hash('sha256', $password) === $u['password']) {
      $_SESSION['user_id'] = (int)$u['id'];
      $_SESSION['role'] = $u['role'];
      $_SESSION['email'] = $u['email'];

      header('Location: index.php');
      exit;
    } else {
      $error = 'Λάθος στοιχεία';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Πιστοποίηση</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Πιστοποίηση</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">
      <div class="content">

        <?php if ($error): ?>
          <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="formbox">
          <form method="post">

          <label class="label">Διεύθυνση e-mail</label>
            <input class="input" type="email" name="email" required>

            <label class="label">Κωδικός Πρόσβασης</label>
            <input class="input" type="password" name="password" required>

            <button class="btn" type="submit">Είσοδος</button>
          </form>
</div>

      </div>
    </div>

  </div>
</div>

</body>
</html>