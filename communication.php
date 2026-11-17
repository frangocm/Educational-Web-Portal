<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

require 'config.php';

$sent   = $_GET['sent'] ?? null;          
$reason = $_GET['reason'] ?? null;        
$mailId = isset($_GET['mail_id']) ? (int)$_GET['mail_id'] : 0;

function h($s){
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}


$tutorEmails = [];
try {
  $stTut = $pdo->prepare("SELECT email FROM users WHERE role='tutor' ORDER BY email");
  $stTut->execute();
  $tutorEmails = $stTut->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
  $tutorEmails = [];
}


$logRow = null;
if ($mailId > 0) {
  try {
    $st = $pdo->prepare("SELECT id, sender, subject, body, recipients, status, created_at FROM mail_log WHERE id = ?");
    $st->execute([$mailId]);
    $logRow = $st->fetch(PDO::FETCH_ASSOC) ?: null;
  } catch (Exception $e) {
    $logRow = null;
  }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Επικοινωνία</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Επικοινωνία</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

      <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">

        <?php if ($sent === '1'): ?>
          <div class="card" style="border-left:6px solid rgba(34,197,94,0.65);">
            <p class="p" style="margin:0; font-weight:800;">
               Η αποστολή καταγράφηκε επιτυχώς.
            </p>

            <?php if ($logRow): ?>
              <p class="p" style="margin:10px 0 0; color: rgba(15,23,42,0.70);">
                <span class="bold">Απόδειξη (mail_log):</span>
                ID #<?= (int)$logRow['id'] ?>
                <?php if (!empty($logRow['created_at'])): ?>
                  • <?= h($logRow['created_at']) ?>
                <?php endif; ?>
              </p>

              <div class="card" style="margin-top:12px; background: rgba(245,247,255,0.65);">
                <p class="p" style="margin:0;">
                  <span class="bold">Αποστολέας:</span>
                  <?= h($logRow['sender']) ?>
                </p>
                <p class="p" style="margin:8px 0 0;">
                  <span class="bold">Παραλήπτες (tutors):</span>
                  <?= h($logRow['recipients']) ?>
                </p>
                <p class="p" style="margin:8px 0 0;">
                  <span class="bold">Θέμα:</span>
                  <?= h($logRow['subject']) ?>
                </p>
              </div>

              <p class="p" style="margin:10px 0 0; color: rgba(15,23,42,0.68);">
                Σημείωση: Σε τοπικό περιβάλλον (MAMP) μπορεί να μην υπάρχει ρυθμισμένος mail server.
                Η εργασία θεωρείται ότι “στάλθηκε” όταν υπάρχει καταγραφή στη βάση (mail_log).
              </p>
            <?php else: ?>
              <p class="p" style="margin:10px 0 0; color: rgba(15,23,42,0.68);">
                (Δεν βρέθηκε εγγραφή mail_log για εμφάνιση.)
              </p>
            <?php endif; ?>
          </div>

        <?php elseif ($sent === '0'): ?>
          <div class="card" style="border-left:6px solid rgba(180,35,24,0.70);">
            <p class="p" style="margin:0; font-weight:800; color:#b42318;">
               Αποτυχία αποστολής / καταγραφής.
            </p>

            <p class="p" style="margin:8px 0 0; color: rgba(15,23,42,0.68);">
              <?php if ($reason === 'missing'): ?>
                Λείπουν στοιχεία (αποστολέας, θέμα ή κείμενο).
              <?php elseif ($reason === 'bad_email'): ?>
                Το e-mail αποστολέα δεν είναι έγκυρο.
              <?php elseif ($reason === 'sender_not_found'): ?>
                Το e-mail αποστολέα δεν υπάρχει στη βάση δεδομένων.
              <?php elseif ($reason === 'sender_mismatch'): ?>
                Το e-mail αποστολέα δεν ταιριάζει με τον συνδεδεμένο χρήστη.
              <?php elseif ($reason === 'notutors'): ?>
                Δεν υπάρχουν tutors στη βάση για παραλήπτες.
              <?php else: ?>
                Αποτυχία καταγραφής/αποστολής.
              <?php endif; ?>
            </p>
          </div>
        <?php endif; ?>

        <div class="card">
          <p class="p" style="margin:0;">
            Η συγκεκριμένη φόρμα λειτουργεί σε συνεργασία με PHP script και αποστέλλει e-mail
            σε όλους τους χρήστες που είναι πιστοποιημένοι ως <span class="bold">tutors</span>.
          </p>
        </div>

        
        <div class="card">
          <h2 class="h2" style="margin-top:0;">Παραλήπτες </h2>

          <?php if (!empty($tutorEmails)): ?>
            <p class="p" style="margin:0; color: rgba(15,23,42,0.70);">
              Το μήνυμα θα καταγραφεί/σταλεί στους παρακάτω tutors (emails από τη βάση):
            </p>

            <div class="tools" style="margin-top:10px;">
              <?php foreach ($tutorEmails as $tEmail): ?>
                <span class="tool-link"><?= h($tEmail) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="p" style="margin:0; color:#b42318; font-weight:800;">
              Δεν βρέθηκαν tutors (role='tutor').
            </p>
          <?php endif; ?>
        </div>

        <div class="card">
          <h2 class="h2" style="margin-top:0;">Αποστολή e-mail μέσω φόρμας</h2>

          <div class="formbox" style="margin: 10px auto 0;">
            <form method="post" action="send_mail.php">

              <label class="label">Αποστολέας </label>
              <input class="input" type="email" name="sender" required>

              <label class="label">Θέμα</label>
              <input class="input" type="text" name="subject" required>

              <label class="label">Κείμενο</label>
              <textarea class="input" name="message" rows="6" required></textarea>

              <button class="btn" type="submit">Αποστολή</button>
            </form>
          </div>
        </div>

        <div class="card">
          <h2 class="h2" style="margin-top:0;">Αποστολή e-mail με χρήση e-mail διεύθυνσης</h2>
          <p class="p" style="margin:0;">
            Εναλλακτικά μπορείτε να αποστείλετε e-mail στην παρακάτω διεύθυνση:
            <a href="mailto:tutor@csd.auth.test.gr"><span class="bold">tutor@csd.auth.test.gr</span></a>
          </p>
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
        <div class="footer-right">
          © 2026 • Όλα τα δικαιώματα διατηρούνται
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>