<?php
function render_header($active_page = 'home', $alert_message = '') {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TutorConnect HK</title>
<style>
  html { 
    font-size: <?= $_SESSION['font_size'] ?>; 
  }
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="<?php if ($_SESSION['high_contrast'] == true) { echo 'high-contrast'; } ?>">

<header class="navbar">
<div class="nav-logo">
  <a href="?page=home">
    <img src="logo.png" alt="HK TutorConnect Logo">
    <div>
      <h1>TutorConnect</h1>
      <small>&nbsp;For Hong Kong</small>
    </div>
  </a>
</div>

<nav class="nav-links">
  <a href="?page=home" class="<?= $active_page == 'home' ? 'active' : '' ?>">Home</a>
  <a href="?page=search" class="<?= $active_page == 'search' ? 'active' : '' ?>">Find Tutors</a>
        
  <?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($_SESSION['user_role'] == 'Student' || $_SESSION['user_role'] == 'Parent'): ?>
      <a href="?page=bookings" class="<?= $active_page == 'bookings' ? 'active' : '' ?>">My Bookings</a>
      <a href="?page=messages" class="<?= $active_page == 'messages' ? 'active' : '' ?>">Messages</a>
    <?php elseif ($_SESSION['user_role'] == 'Tutor'): ?>
      <a href="?page=tutor_dashboardd" class="<?= $active_page == 'tutor_dashboardd' ? 'active' : '' ?>">Tutor Dashboard</a>
      <a href="?page=profile" class="<?= $active_page == 'profile' ? 'active' : '' ?>">Edit Profile</a>
      <a href="?page=messages" class="<?= $active_page == 'messages' ? 'active' : '' ?>">Messages</a>
    <?php elseif ($_SESSION['user_role'] == 'Admin'): ?>
      <a href="?page=admin" class="<?= $active_page == 'admin' ? 'active' : '' ?>">Admin Panel</a>
    <?php endif; ?>
  <?php else: ?>
    <a href="?page=login" class="<?= $active_page == 'login' ? 'active' : '' ?>">Login</a>
    <a href="?page=register" class="<?= $active_page == 'register' ? 'active' : '' ?>">Create Account</a>
  <?php endif; ?>
</nav>

<div style="display:flex; align-items:center; gap:0.75rem;">
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="user-status-box">
      <span>👤 <?= $_SESSION['user_name'] ?> (<?= $_SESSION['user_role'] ?>)</span>
      <a href="?action=logout" style="color:#ef4444; font-weight:bold; text-decoration:none;">Logout</a>
    </div>
  <?php endif; ?>

  <a href="?set_font=small&page=<?= $active_page ?>" class="btn-sec">A-</a>
  <a href="?set_font=medium&page=<?= $active_page ?>" class="btn-sec">A</a>
  <a href="?set_font=large&page=<?= $active_page ?>" class="btn-sec">A+</a>
  <a href="?toggle_contrast=1&page=<?= $active_page ?>" class="btn-sec" title="Toggle High Contrast Mode">◐</a>
</div>
</header>

<main class="main-container">
<?php if (!empty($alert_message)): ?>
  <?= $alert_message ?>
<?php endif; ?>
<?php
}

function render_footer() {
?>

</main>

<footer style="text-align: center; padding: 2rem; border-top: 1px solid var(--border-color); margin-top: 3rem; font-size: 0.85rem; color: var(--text-color);">
<p>Information and Communication Technology (ICT) SBA Project</p>
<p>Developed by Ng Cheung Kee - 6C(19)</p>
</footer>

</body>
</html>
<?php
}