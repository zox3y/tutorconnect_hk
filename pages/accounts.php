<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];

  if ($action == 'user_login') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
      $alert_message = "<div class='alert-error'>Error: Please enter both email address and password.</div>";
    } else {
      $safe_email = safe($email);
      $sql = "SELECT UserID, Title, Name, Email, PasswordHash, Role FROM USER WHERE Email = '$safe_email'";
      $result = $conn->query($sql);
      $user_row = $result ? $result->fetch_assoc() : null;

      if ($user_row && password_verify($password, $user_row['PasswordHash'])) {
        $_SESSION['user_id'] = $user_row['UserID'];
        $_SESSION['user_role'] = $user_row['Role'];
        
        if (!empty($user_row['Title'])) {
          $display_name = $user_row['Title'] . " " . $user_row['Name'];
        } else {
          $display_name = $user_row['Name'];
        }
        $_SESSION['user_name'] = $display_name;

        header("Location: " . $_SERVER['PHP_SELF'] . "?page=search");
        exit();
      } else {
        $alert_message = "<div class='alert-error'>Error: Invalid email or password.</div>";
      }
    }
  }


  if ($action == 'create_account_1') {
    $title = trim($_POST['title']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];
    $phone = trim($_POST['phone']);
    $district = trim($_POST['district']);

    $valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $valid_phone = empty($phone) || preg_match('/^[0-9]{8}$/', $phone);

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
      $alert_message = "<div class='alert-error'>Error: Please fill in all required fields marked with *.</div>";
    } else if (!$valid_email) {
      $alert_message = "<div class='alert-error'>Error: Please enter a valid email address.</div>";
    } else if (!$valid_phone) {
      $alert_message = "<div class='alert-error'>Error: Phone number must be 8 digits.</div>";
    } else if ($password !== $confirm_password) {
      $alert_message = "<div class='alert-error'>Error: Passwords do not match.</div>";
    } else {
      $safe_email = safe($email);
      $sql_email = "SELECT COUNT(*) FROM USER WHERE Email = '$safe_email'";
      $res_check = $conn->query($sql_email);
      $row_check = $res_check ? $res_check->fetch_row() : [0];
      $email_exists = $row_check[0] > 0;

      if ($email_exists) {
        $alert_message = "<div class='alert-error'>Error: This email address is already registered.</div>";
      } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        if (!empty($title)) {
          $full_name = $title . " " . $name;
        } else {
          $full_name = $name;
        }

        if ($role == 'Tutor') {
          $_SESSION['pending'] = [
            'title' => $title,
            'name' => $name,
            'email' => $email,
            'password_hash' => $password_hash,
            'role' => $role,
            'phone' => $phone,
            'district' => $district
          ];
          header("Location: " . $_SERVER['PHP_SELF'] . "?page=register&step=2");
          exit();
        } else {
          $safe_title    = safe($title);
          $safe_name     = safe($name);
          $safe_email    = safe($email);
          $safe_role     = safe($role);
          $safe_phone    = safe($phone);
          $safe_district = safe($district);
          $sql_insert = "INSERT INTO USER (Title, Name, Email, PasswordHash, Role, Phone, District) VALUES ('$safe_title', '$safe_name', '$safe_email', '$password_hash', '$safe_role', '$safe_phone', '$safe_district')";
          $conn->query($sql_insert);

          $_SESSION['user_id'] = $conn->insert_id;
          $_SESSION['user_role'] = $role;
          $_SESSION['user_name'] = $full_name;

          header("Location: " . $_SERVER['PHP_SELF'] . "?page=search");
          exit();
        }
      }
    }
  }

  // tutor login only

  if ($action == 'create_account_2') {
    if (isset($_SESSION['pending'])) {
      $reg_data = $_SESSION['pending'];
      $hourly_rate = (float)$_POST['hourly_rate'];
      $qualifications = trim($_POST['qualifications']);
      $bio = trim($_POST['bio']);
      
      if (isset($_POST['selected_subjects'])) {
        $selected_subjects = (array)$_POST['selected_subjects'];
      } else {
        $selected_subjects = [];
      }

      if ($hourly_rate <= 0 || $hourly_rate >= 9999) {
        $hourly_rate = 250.00;
      }

      $safe_title    = safe($reg_data['title']);
      $safe_name     = safe($reg_data['name']);
      $safe_email    = safe($reg_data['email']);
      $safe_phone    = safe($reg_data['phone']);
      $safe_district = safe($reg_data['district']);
      $sql_user = "INSERT INTO USER (Title, Name, Email, PasswordHash, Role, Phone, District) VALUES ('$safe_title', '$safe_name', '$safe_email', '{$reg_data['password_hash']}', 'Tutor', '$safe_phone', '$safe_district')";

      $conn->query($sql_user);

      $new_tutor_id = (int)$conn->insert_id;
      $safe_bio    = safe($bio);
      $safe_quals  = safe($qualifications);
      $sql_tutor = "INSERT INTO TUTOR (TutorID, Bio, Qualifications, HourlyRate, AvgRating) VALUES ($new_tutor_id, '$safe_bio', '$safe_quals', $hourly_rate, 0.00)";

      $conn->query($sql_tutor);

      if (!empty($selected_subjects)) {
        foreach ($selected_subjects as $subj_id) {
          $subj_id = (int)$subj_id;
          $conn->query("INSERT INTO TUTOR_SUBJECT (TutorID, SubjectID) VALUES ($new_tutor_id, $subj_id)");
        }
      }

      $_SESSION['user_id'] = $new_tutor_id;
      $_SESSION['user_role'] = 'Tutor';
      if (!empty($reg_data['title'])) {
        $_SESSION['user_name'] = $reg_data['title'] . " " . $reg_data['name'];
      } else {
        $_SESSION['user_name'] = $reg_data['name'];
      }
      unset($_SESSION['pending']);

      header("Location: " . $_SERVER['PHP_SELF'] . "?page=tutor_dashboardd");
      exit();
    }
  }
}

if (!empty($alert_message)) {
    echo $alert_message;
}

if ($active_page == 'login') {
  ?>
  <h2>User Authentication</h2>
  <div class='card' style='max-width:450px; margin: 0 auto;'>
  <form method='POST' action='?page=login' class='form-vertical'>
    <input type='hidden' name='action' value='user_login'>
    <h3>Account Login</h3>
      
    <div class='form-group'>
      <label>Email Address</label>
      <input type='email' name='email' required placeholder='howareyou@example.com'>
    </div>

    <div class='form-group'>
      <label>Password</label>
      <input type='password' name='password' required placeholder='Enter your password'>
    </div>

    <button type='submit' class='btn-primary'>SIGN IN</button>
  </form>
  </div>
  <?php
} elseif ($active_page == 'register') {
  if (isset($_GET['step'])) {
    $reg_step = (int)$_GET['step'];
  } else {
    $reg_step = 1;
  }

  if ($reg_step == 2 && isset($_SESSION['pending'])) {
    ?>
  <h2>Tutor Registration</h2>
  <p>Please fill in the information below.</p>

  <div class='card'>
    <form method='POST' action='?page=register&step=2' class='form-vertical'>
      <input type='hidden' name='action' value='create_account_2'>
        
      <div class='form-group'>
        <label>Hourly Rate ($ HKD) *</label>
        <input type='number' name='hourly_rate' min='50' max='9999' step='10' value='250' required>
      </div>

      <div class='form-group'>
        <label>Qualifications</label>
        <input type='text' name='qualifications' placeholder='ie: B.Sc Mathematics (HKU), 2012 DSE 5**'>
      </div>

      <div class='form-group'>
        <label>Select Subject Specialization(s):</label>
        <div class='subject-selection-box'>
          <?php foreach ($subjects as $category_name => $subjects_in_cat): ?>
            <div class='subject-category-header'><?= $category_name ?></div>
            <div class='subject-checkbox-grid'>
            <?php foreach ($subjects_in_cat as $subj_item): ?>
              <label class='checkbox-label'>
                <input type='checkbox' name='selected_subjects[]' value='<?= $subj_item['SubjectID'] ?>'>
                <?= $subj_item['SubjectName'] ?>
              </label>
            <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class='form-group'>
        <label>Bio</label>
        <textarea name='bio' rows='3' placeholder='Describe your teaching experience...'></textarea>
      </div>

      <button type='submit' class='btn-primary' style='margin-top:0.5rem;'>COMPLETE REGISTRATION</button>
    </form>
  </div>
  <?php
  } else {
    ?>
  <h2>Create New Account</h2>
  <p>Please fill in your personal information below.</p>
    
  <div class='card'>
    <form method='POST' action='?page=register&step=1' class='form-vertical'>
      <input type='hidden' name='action' value='create_account_1'>
        
      <div class='form-group'>
        <label>Title (Optional)</label>
        <select name='title'>
          <option value=''>-- None / Unspecified --</option>
          <option value='Mr.'>Mr.</option>
          <option value='Mrs.'>Mrs.</option>
          <option value='Ms.'>Ms.</option>
          <option value='Dr.'>Dr.</option>
          <option value='Prof.'>Prof.</option>
        </select>
      </div>

      <div class='form-group'>
        <label>Full Name *</label>
        <input type='text' name='name' required placeholder='ie: Chris Wong'>
      </div>

      <div class='form-group'>
        <label>Email Address *</label>
        <input type='email' name='email' required placeholder='howareyou@example.com'>
      </div>

      <div class='form-group'>
        <label>Password *</label>
        <input type='password' name='password' required placeholder='Enter password'>
      </div>

      <div class='form-group'>
        <label>Verify Password *</label>
        <input type='password' name='confirm_password' required placeholder='Re-enter password'>
      </div>

      <div class='form-group'>
        <label>Role *</label>
        <select name='role'>
          <option value='Student'>Student</option>
          <option value='Parent'>Parent</option>
          <option value='Tutor'>Tutor</option>
        </select>
      </div>

      <div class='form-group'>
        <label>Phone Number</label>
        <input type='text' name='phone' placeholder='ie: 66668888' maxlength='8'>
      </div>

      <div class='form-group'>
        <label>District / Area</label>
        <?php district_dropdown('district'); ?>
      </div>

      <button type='submit' class='btn-primary' style='margin-top:0.5rem;'>CONTINUE</button>
    </form>
  </div>
  <?php
  }
}