<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Αρχική σελίδα</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a id="top"></a>

<div class="wrapper">
  <div class="shell">

    <div class="header">
      <h1 class="page-title">Αρχική σελίδα</h1>
      <div class="title-line"></div>
    </div>

    <div class="main">

    <?php include __DIR__ . '/menu.php'; ?>

      <section class="content">
        <h2 class="h2">Καλωσορίσατε</h2>

        <p class="p">
          Καλωσορίσατε στον ιστοχώρο του μαθήματος.
          Ο παρών ιστοχώρος έχει στόχο την υποστήριξη της εκμάθησης της HTML.
        </p>

        <p class="p">
          Στην ιστοσελίδα <span class="bold">Ανακοινώσεις</span> θα βρείτε πληροφορίες σχετικά με το μάθημα,
          στην <span class="bold">Επικοινωνία</span> στοιχεία επικοινωνίας,
          στα <span class="bold">Έγγραφα μαθήματος</span> εκπαιδευτικό υλικό
          και στις <span class="bold">Εργασίες</span> τις εκφωνήσεις εργασιών.
        </p>

        <div class="card" style="text-align:center;">
          <img src="image/html.png" alt="HTML" style="max-width:320px; width:100%; height:auto;">
        </div>
      </section>

    </div>

    
    <div class="top-link">
      <a href="#top">TOP</a>
    </div>

    <div class="footer">
  <div class="footer-inner">

    <div class="footer-left">
      Εκπαιδευτικά Περιβάλλοντα Διαδικτύου
      <div class="sub">Τμήμα Πληροφορικής – Πανεπιστήμιο</div>
    </div>

    <div class="footer-right">
      © 2026 • Όλα τα δικαιώματα διατηρούνται
    </div>

  </div>
</div>

    </div>

  </div>
</div>

</body>
</html>

