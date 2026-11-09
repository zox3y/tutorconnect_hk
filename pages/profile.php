<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_tutor_profile') {
  if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'Tutor') {
    $tutor_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $district = trim($_POST['district']);
    $bio = trim($_POST['bio']);
    $qualifications = trim($_POST['qualifications']);
    $hourly_rate = (float)$_POST['hourly_rate'];
     
    if (isset($_POST['selected_subjects'])) {
      $selected_subjects = (array)$_POST['selected_subjects'];
    } else {
      $selected_subjects = [];
    }

    $valid_phone = empty($phone) || preg_match('/^[0-9]{8}$/', $phone);

    if (empty($name)) {
      $alert_message = "<div class='alert-error'>Error: Name cannot be empty.</div>";
    } else if (!$valid_phone) {
      $alert_message = "<div class='alert-error'>Error: Phone number must be 8 digits.</div>";
    } else {
      if ($hourly_rate <= 0 || $hourly_rate >= 9999) {
        $hourly_rate = 250.00;
    }

    $sql_user1 = "UPDATE USER SET Title = '$title', Name = '$name', Phone = '$phone', District = '$district' WHERE UserID = $tutor_id";
    $conn->query($sql_user1);

    $sql_tutor1 = "UPDATE TUTOR SET Bio = '$bio', Qualifications = '$qualifications', HourlyRate = $hourly_rate WHERE TutorID = $tutor_id";
    $conn->query($sql_tutor1);

    $conn->query("DELETE FROM TUTOR_SUBJECT WHERE TutorID = $tutor_id");

    if (!empty($selected_subjects)) {
      foreach ($selected_subjects as $subj_id) {
        $subj_id = (int)$subj_id;
        $conn->query("INSERT INTO TUTOR_SUBJECT (TutorID, SubjectID) VALUES ($tutor_id, $subj_id)");
      }
    }

    // file uploadingg
    if (isset($_FILES['qualification_doc'])) {
      if ($_FILES['qualification_doc']['error'] == UPLOAD_ERR_OK) {
        $temp = $_FILES['qualification_doc']['tmp_name'];
        $og_name = $_FILES['qualification_doc']['name'];
        $file_size = $_FILES['qualification_doc']['size'];
            
        // <5mb(5*1024^2) File size check

        if ($file_size <= 5 * 1024 * 1024) {
          $file_ext = strtolower(pathinfo($og_name, PATHINFO_EXTENSION));
          $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed)) {
          $upload_dir = __DIR__ . '/../uploads/';
               
          if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
          }

          $new_filename = uniqid() . '.' . $file_ext;		// to fix duplicate file names overwriting each other
          $dest = $upload_dir . $new_filename;
          if (move_uploaded_file($temp, $dest)) {
            $sql_doc = "INSERT INTO TUTOR_DOCUMENT (TutorID, FilePath, OriginalName, Status) 
              VALUES ($tutor_id, 'uploads/$new_filename', '$og_name', 'Pending')";
            $conn->query($sql_doc);
              }
            } else {
            $alert_message = "<div class='alert-error'>Upload Error: Invalid file format.</div>";
            }
          } else {
            $alert_message = "<div class='alert-error'>Upload Error: File size exceeds 5 MB.</div>";
          }
        }
      }

      if (!empty($title)) {
        $full_name = $title . " " . $name;
      } else {
        $full_name = $name;
      }
      $_SESSION['user_name'] = $full_name;
        if (empty($alert_message)) {
          $alert_message = "<div class='alert-success'>Updated successfully!</div>";
      }
    }
  }
}


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Tutor') {
    ?>
<div class='alert-error'>Access Denied.</div>
<?php
    return;
}

$tutor_id = $_SESSION['user_id'];

// sql part

$sql_fetch_tutor = "
  SELECT u.Title, u.Name, u.Phone, u.District, t.Bio, t.Qualifications, t.HourlyRate 
  FROM USER u 
  JOIN TUTOR t ON u.UserID = t.TutorID 
  WHERE u.UserID = $tutor_id
";
$res = $conn->query($sql_fetch_tutor);
$profile = $res ? $res->fetch_assoc() : [];

$sql_my_subj = "SELECT SubjectID FROM TUTOR_SUBJECT WHERE TutorID = $tutor_id";
$my_subject_ids = array_column(fetch_rows($conn->query($sql_my_subj)), 'SubjectID');

$sql_my_docs = "SELECT DocID, FilePath, OriginalName, Status, UploadedAt FROM TUTOR_DOCUMENT WHERE TutorID = $tutor_id ORDER BY UploadedAt DESC";
$docs = fetch_rows($conn->query($sql_my_docs));

if (!empty($alert_message)) {
    echo $alert_message;
}

?>

<h2>Edit Tutor Profile</h2>
<div class='card'>
<form method='POST' action='?page=profile' enctype='multipart/form-data' class='form-vertical'>
<input type='hidden' name='action' value='update_tutor_profile'>

<h3>Personal Details</h3>

<div class='form-group'>
  <label>Title</label>
  <select name='title'>
    <?php
      $titles = ['', 'Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.'];
        foreach ($titles as $t_opt) {
          $sel = ($profile['Title'] === $t_opt) ? 'selected' : '';
          $label = empty($t_opt) ? '-- None / Unspecified --' : $t_opt;
    ?>
      <option value='<?= $t_opt ?>' <?= $sel ?>><?= $label ?></option>
      <?php
    }
    ?>
  </select>
</div>

<div class='form-group'>
  <label>Full Name *</label>
  <input type='text' name='name' required value='<?= $profile['Name'] ?>'>
</div>

<div class='form-group'>
  <label>Phone Number</label>
  <input type='text' name='phone' value='<?= $profile['Phone'] ?>' maxlength='8'>
</div>

<div class='form-group'>
  <label>District / Area</label>
  <?php district_dropdown('district', $profile['District']); ?>
</div>

<h3 style='border-top:1px solid var(--border-color); padding-top:1rem; margin-top:0.5rem;'>Profile</h3>

<div class='form-group'>
  <label>Hourly Rate ($ HKD) *</label>
  <input type='number' name='hourly_rate' min='50' max='9999' step='10' value='<?= $profile['HourlyRate'] ?>'>
</div>

<div class='form-group'>
  <label>Qualifications</label>
  <input type='text' name='qualifications' value='<?= $profile['Qualifications'] ?>'>
</div>

<div class='form-group'>
  <label>Select Subject Specialization(s):</label>
  <div class='subject-selection-box'>
    <?php foreach ($subjects as $category_name => $subjs): ?>
    <div class='subject-category-header'><?= $category_name ?></div>
    <div class='subject-checkbox-grid'>
      <?php foreach ($subjs as $subj_item): ?>
        <?php $checked = in_array($subj_item['SubjectID'], $my_subject_ids) ? 'checked' : ''; ?>
        <label class='checkbox-label'>
          <input type='checkbox' name='selected_subjects[]' value='<?= $subj_item['SubjectID'] ?>' <?= $checked ?>>
          <?= $subj_item['SubjectName'] ?>
        </label>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class='form-group'>
  <label>Bio</label>
  <textarea name='bio' rows='4'><?= $profile['Bio'] ?></textarea>
</div>

<h3 style='border-top:1px solid var(--border-color); padding-top:1rem; margin-top:0.5rem;'>Verify Qualification(s)</h3>
      
<div class='form-group'>
  <label>Upload Qualification Proof / Certificate (PDF, JPG, PNG - Max 5MB):</label>
  <input type='file' name='qualification_doc' accept='.pdf,.jpg,.jpeg,.png'>
</div>

<?php if (!empty($docs)): ?>
  <div style='margin-top:0.5rem;'>
    <strong>Uploaded Records:</strong>
    <ul style='padding-left:1.2rem; margin-top:0.25rem;'>
    <?php foreach ($docs as $mydoc): ?>
    <?php
      if ($mydoc['Status'] == 'Approved') {
        $status_color = '#15803d';
        } else if ($mydoc['Status'] == 'Rejected') {
          $status_color = '#b91c1c';
        } else {
          $status_color = '#92400e';
        }
    ?>
    <li>

    <a href='<?= $mydoc['FilePath'] ?>' target='_blank'><?= $mydoc['OriginalName'] ?></a> - 
    <strong style='color:<?= $status_color ?>;'><?= $mydoc['Status'] ?></strong> 
      (<?= $mydoc['UploadedAt'] ?>)
    </li>
  <?php endforeach; ?>
  </ul>
  </div>
<?php endif; ?>

<button type='submit' class='btn-primary' style='margin-top:1rem;'>SAVE & UPLOAD</button>
</form>
</div>