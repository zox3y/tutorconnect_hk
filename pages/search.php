<?php
if (isset($_GET['kw'])) {
  $keyword = trim($_GET['kw']);
} else {
    $keyword = '';
}

if (isset($_GET['subj'])) {
  $subject_id = (int)$_GET['subj'];
} else {
  $subject_id = 0;
}

if (isset($_GET['district'])) {
  $district = trim($_GET['district']);
} else {
  $district = '';
}

if (isset($_GET['rate']) && $_GET['rate'] !== '') {
  $max_rate = (float)$_GET['rate'];
} else {
  $max_rate = 9999.0;
}

$sql_search = "
  SELECT u.UserID, u.Title, u.Name, u.District, t.Bio, t.Qualifications, t.HourlyRate, t.AvgRating,
    GROUP_CONCAT(s.SubjectName SEPARATOR ', ') AS SubjectsTaught,
    EXISTS(SELECT 1 FROM TUTOR_DOCUMENT WHERE TutorID = u.UserID AND Status = 'Approved') AS IsVerified
  FROM TUTOR t
  JOIN USER u ON t.TutorID = u.UserID
  LEFT JOIN TUTOR_SUBJECT ts ON t.TutorID = ts.TutorID
  LEFT JOIN SUBJECT s ON ts.SubjectID = s.SubjectID
  WHERE t.HourlyRate <= $max_rate
";

if (!empty($keyword)) {
  $safe_keyword = safe($keyword);
  $sql_search .= " AND (u.Name LIKE '%$safe_keyword%' OR t.Bio LIKE '%$safe_keyword%' OR t.Qualifications LIKE '%$safe_keyword%')";
}

if (!empty($district)) {
  $safe_district = safe($district);
  $sql_search .= " AND u.District = '$safe_district'";
}

if ($subject_id > 0) {
  $sql_search .= " AND t.TutorID IN (SELECT TutorID FROM TUTOR_SUBJECT WHERE SubjectID = $subject_id)";
}

$sql_search .= " GROUP BY t.TutorID ORDER BY t.AvgRating DESC, u.Name ASC";

$tutors = fetch_rows($conn->query($sql_search));

if (!empty($alert_message)) {
    echo $alert_message;
}

?>

<h2>Find Hong Kong Tutors</h2>

<form method='GET' action='' class='card'>
<input type='hidden' name='page' value='search'>
<div class='form-grid'>
<div>
  <label>Keyword Search:</label>
  <input type='text' name='kw' value='<?= $keyword ?>' placeholder='Name, subject, qualification...'>
</div>
        
<div>
  <label>Subject:</label>
  <select name='subj'>
    <option value='0'>-- All Subjects --</option>
    <?php foreach ($subjects as $cat_name => $subj_list): ?>
      <optgroup label='<?= $cat_name ?>'>
        <?php foreach ($subj_list as $s_row): ?>
          <?php $selected_flag = ($subject_id == $s_row['SubjectID']) ? 'selected' : ''; ?>
          <option value='<?= $s_row['SubjectID'] ?>' <?= $selected_flag ?>><?= $s_row['SubjectName'] ?></option>
          <?php endforeach; ?>
      </optgroup>
    <?php endforeach; ?>
  </select>
</div>

<div>
  <label>District / Area:</label>
  <?php district_dropdown('district', $district, '-- All Districts --'); ?>
</div>

<div>
  <label>Max Hourly Rate ($):</label>
  <input type='number' name='rate' value='<?= $max_rate ?>' min='0' max='9999'>
</div>
</div><br>
<button type='submit' class='btn-primary' style='font-size:1rem;'>Apply Filter</button> 
<a href='?page=search' class='btn-sec'>Reset</a>
</form>

<div style='display:flex; flex-direction:column; gap:1rem;'>
<?php if (count($tutors) == 0): ?>
<p>No tutors match the selected search criteria.</p>
<?php else: ?>
<?php foreach ($tutors as $tutor): ?>
  <?php
    $subjects_text = $tutor['SubjectsTaught'] ? $tutor['SubjectsTaught'] : 'Unspecified Core Subjects';
    $avg = (float)$tutor['AvgRating'];
    $rating_1 = number_format($avg, 2);
    $price = number_format($tutor['HourlyRate'], 2);
    $tutor_display_name = !empty($tutor['Title']) ? $tutor['Title'] . " " . $tutor['Name'] : $tutor['Name'];
    $badge_class = rating_level($avg);
    $location = !empty($tutor['District']) ? $tutor['District'] : 'Hong Kong';
    $verified = (bool)$tutor['IsVerified'];
  ?>

  <div class='card'>
    <div style='display:flex; justify-content:space-between; align-items:center;'>
      <h3 style='margin:0;'><?= $tutor_display_name ?>
        <?php if ($verified): ?>
          <span class='verified-badge'>✔ Verified</span>
        <?php endif; ?>
      </h3>
      <span class='rating-badge <?= $badge_class ?>'>★ <?= $rating_1 ?> / 5.00</span>
    </div>
                
    <p style='margin: 0.5rem 0 0.25rem 0;'><strong>Subject(s) offered:</strong> <?= $subjects_text ?></p>
    <p style='margin: 0.25rem 0;'><strong>Qualifications:</strong> <?= $tutor['Qualifications'] ?></p>
    <p style='margin: 0.25rem 0;'><strong>Location:</strong> <?= $location ?></p>
    <p style='margin: 0.25rem 0 0.75rem 0;'><?= $tutor['Bio'] ?></p>
    <div style='display:flex; justify-content:space-between; align-items:center;'>
      <strong>$<?= $price ?> / hour</strong>
        <?php if (isset($_SESSION['user_role'])): ?>
          <?php if ($_SESSION['user_role'] == 'Student' || $_SESSION['user_role'] == 'Parent'): ?>
            <div style='display:flex; gap:0.5rem; align-items:center;'>
              <a href='?page=bookings&book_tutor_id=<?= $tutor['UserID'] ?>' class='btn-primary'>Book Lesson</a>
              <a href='?page=messages&chat_with=<?= $tutor['UserID'] ?>' class='btn-primary' style='background-color:#16a34a; width:2.7rem; height:2.7rem; padding:0; display:inline-flex; align-items:center; justify-content:center; font-size:1.1rem;' title='Message Tutor'>&#9993;</a>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <a href='?page=login' class='btn-sec'>Login to Book</a>
        <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
<?php endif; ?>
</div>
