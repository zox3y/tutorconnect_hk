<?php
// ICT S6 SBA Project: TutorConnect HK
// Name: Ng Cheung Kee
// Class: 6C (19)

session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/layout.php';

if (!isset($_SESSION['font_size'])) {
  $_SESSION['font_size'] = '16px';
}
if (!isset($_SESSION['high_contrast'])) {
  $_SESSION['high_contrast'] = false;
}

if (isset($_GET['set_font'])) {
  $font_choice = $_GET['set_font'];
  if ($font_choice == 'small') {
    $_SESSION['font_size'] = '14px';
  }
  if ($font_choice == 'medium') {
    $_SESSION['font_size'] = '16px';
  }
  if ($font_choice == 'large') {
    $_SESSION['font_size'] = '18px';
  }
    
  if (isset($_GET['page'])) {
    $redirect_page = $_GET['page'];
  } else {
    $redirect_page = 'home';
 }
    
  header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $redirect_page);
  exit();
}

// contrast/dark mode

if (isset($_GET['toggle_contrast'])) {
  if ($_SESSION['high_contrast'] === true) {
    $_SESSION['high_contrast'] = false;
  } else {
    $_SESSION['high_contrast'] = true;
 }
  
  if (isset($_GET['page'])) {
    $redirect_page = $_GET['page'];
  } else {
    $redirect_page = 'home';
  }
  
  header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $redirect_page);
  exit();
}

// logout

if (isset($_GET['action'])) {
  if ($_GET['action'] == 'logout') {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_role']);
    unset($_SESSION['user_name']);
    unset($_SESSION['pending']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=login");
    exit();
  }
}

$alert_message = '';

// page routing

if (isset($_GET['page'])) {
  $active_page = $_GET['page'];
} else {
  $active_page = 'home';
}

$page_map = [
  'home'             => 'pages/home.php',
  'login'            => 'pages/accounts.php',
  'register'         => 'pages/accounts.php',
  'search'           => 'pages/search.php',
  'profile'          => 'pages/profile.php',
  'bookings'         => 'pages/bookings.php',
  'messages'         => 'pages/messages.php',
  'tutor_dashboardd' => 'pages/tutor_dashboard.php',
  'admin'            => 'pages/admin.php'
];

if (array_key_exists($active_page, $page_map)) {
  $page_file = $page_map[$active_page];
} else {
  $active_page = 'search';
  $page_file = 'pages/search.php';
}

$subjects = get_subjects($conn);

render_header($active_page, $alert_message);
require __DIR__ . '/' . $page_file;
render_footer();