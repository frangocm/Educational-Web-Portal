<?php


function render_page(string $title, string $active, callable $contentFn): void {
  ?>
  <!DOCTYPE html>
  <html lang="el">
  <head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body id="top">
    <div class="wrapper">
      <div class="shell">

        <div class="header">
          <h1 class="page-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
          <div class="title-line"></div>
        </div>

        <div class="main">
          <div class="sidebar">
            <div class="nav">
              <a class="navbtn <?php echo $active==='home' ? 'active':''; ?>" href="index.php">Αρχική σελίδα</a>
              <a class="navbtn <?php echo $active==='ann' ? 'active':''; ?>" href="announcements.php">Ανακοινώσεις</a>
              <a class="navbtn <?php echo $active==='contact' ? 'active':''; ?>" href="contact.php">Επικοινωνία</a>
              <a class="navbtn <?php echo $active==='docs' ? 'active':''; ?>" href="documents.php">Έγγραφα μαθήματος</a>
              <a class="navbtn <?php echo $active==='asg' ? 'active':''; ?>" href="assignments.php">Εργασίες</a>
            </div>
          </div>

          <div class="content">
            <?php $contentFn(); ?>
          </div>
        </div>

      </div>
    </div>
  </body>
  </html>
  <?php
}
