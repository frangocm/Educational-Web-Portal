<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$role = strtolower((string)($_SESSION['role'] ?? ''));
$isTutor = ($role === 'tutor');

$current = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');

function navClass(string $file, string $current): string {
  return 'navbtn' . ($current === $file ? ' active' : '');
}
?>

<aside class="sidebar">
  <nav class="nav">
    <a class="<?= navClass('index.php', $current) ?>" href="index.php">Αρχική σελίδα</a>
    <a class="<?= navClass('announcements.php', $current) ?>" href="announcements.php">Ανακοινώσεις</a>
    <a class="<?= navClass('communication.php', $current) ?>" href="communication.php">Επικοινωνία</a>
    <a class="<?= navClass('documents.php', $current) ?>" href="documents.php">Έγγραφα μαθήματος</a>
    <a class="<?= navClass('homework.php', $current) ?>" href="homework.php">Εργασίες</a>

    <?php if ($isTutor): ?>
      <a class="<?= navClass('users.php', $current) ?>" href="users.php">Διαχείριση χρηστών</a>
    <?php endif; ?>

    <a class="<?= navClass('logout.php', $current) ?>" href="logout.php">Logout</a>
  </nav>
</aside>

