<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];

  if ($action == 'create_booking') {
    if (!isset($_SESSION['user_id'])) {
      $alert_message = "<div class='alert-error'>Error: Please login to book a lesson.</div>";
    } else {
      $student_id = $_SESSION['user_id'];
      $tutor_id = (int)$_POST['tutor_id'];
      $lesson_date = trim($_POST['lesson_date']);
      $lesson_time = trim($_POST['lesson_time']);
      $duration = (float)$_POST['duration'];

      $valid_date = !empty($lesson_date);
      $valid_time = !empty($lesson_time);
      if ($duration >= 0.5 && $duration <= 4.0) {
        $valid_dur = true;
      } else {
        $valid_dur = false;
      }

      if ($valid_date && $valid_time && $valid_dur) {
        $sql_conflict = "SELECT COUNT(*) FROM BOOKING 
           WHERE TutorID = $tutor_id 
             AND LessonDate = '$lesson_date' 
             AND Status != 'Cancelled'
             AND LessonTime = '$lesson_time'";

        $res_conflict = $conn->query($sql_conflict);
        $row_conflict = $res_conflict ? $res_conflict->fetch_row() : [0];

        if ($row_conflict[0] > 0) {
          $alert_message = "<div class='alert-error'>Please select a different time slot.</div>";
        } else {
          $sql_booking = "INSERT INTO BOOKING (StudentID, TutorID, LessonDate, LessonTime, Duration, Status) 
             VALUES ($student_id, $tutor_id, '$lesson_date', '$lesson_time', $duration, 'Pending')";
          $conn->query($sql_booking);
          $alert_message = "<div class='alert-success'>Submitted successfully!</div>";
        }
      } else {
        $alert_message = "<div class='alert-error'>Invalid booking.</div>";
      }
    }
  }

  // confirm/cancel or smth

  if ($action == 'update_booking_status') {
    $booking_id = (int)$_POST['booking_id'];
    $target_status = $_POST['new_status'];
    $current_uid = $_SESSION['user_id'];
    $current_role = $_SESSION['user_role'];

    $sql_fetch_booking = "SELECT * FROM BOOKING WHERE BookingID = $booking_id";
    $booking_res = $conn->query($sql_fetch_booking);
    $booking_data = $booking_res ? $booking_res->fetch_assoc() : null;

    if ($booking_data) {
      $allowed = false;

      if ($current_role == 'Tutor' && $booking_data['TutorID'] == $current_uid) {
        if ($booking_data['Status'] == 'Pending') {
          if ($target_status == 'Confirmed' || $target_status == 'Cancelled') {
            $allowed = true;
          }
        }
      } else if (($current_role == 'Student' || $current_role == 'Parent') && $booking_data['StudentID'] == $current_uid) {
        if ($booking_data['Status'] == 'Pending' || $booking_data['Status'] == 'Confirmed') {
          if ($target_status == 'Cancelled') {
            $allowed = true;
          }
        }
        if ($booking_data['Status'] == 'Confirmed' && $target_status == 'Completed') {
          $allowed = true;
        }
      }

      if ($allowed) {
        $sql_up_status = "UPDATE BOOKING SET Status = '$target_status' WHERE BookingID = $booking_id";
        $conn->query($sql_up_status);
        $alert_message = "<div class='alert-success'>Updated successfully!</div>";
      } else {
        $alert_message = "<div class='alert-error'>No permission.</div>";
      }
    }
  }


  if ($action == 'submit_review') {
    $booking_id = (int)$_POST['booking_id'];
    $tutor_id = (int)$_POST['tutor_id'];
    $rating = (int)$_POST['rating'];

    if ($rating >= 1 && $rating <= 5) {
      $sql_review = "INSERT INTO REVIEW (BookingID, StudentID, TutorID, Rating) 
        VALUES ($booking_id, {$_SESSION['user_id']}, $tutor_id, $rating)";

      $conn->query($sql_review);
      $sql_avg = "SELECT AVG(Rating) AS CalculatedAverage FROM REVIEW WHERE TutorID = $tutor_id";
      $res_avg = $conn->query($sql_avg);
      $row_avg = $res_avg ? $res_avg->fetch_row() : [0];
      $new_avg = $row_avg[0];
      $sql_up_tutor = "UPDATE TUTOR SET AvgRating = $new_avg WHERE TutorID = $tutor_id";
      $conn->query($sql_up_tutor);
      $alert_message = "<div class='alert-success'>Rating submitted!</div>";
    }
  }
}

if (!isset($_SESSION['user_id'])) {
  ?>
  <div class='alert-error'>Please login to access bookings.</div>
  <?php
  return;
}

if (!empty($alert_message)) {
    echo $alert_message;
}
?>

<h2>My Lesson Bookings</h2>

<?php
if (isset($_GET['book_tutor_id'])) {
  $tutor_id = (int)$_GET['book_tutor_id'];
  
  $sql_tutorfetch = "SELECT u.Name, u.Title, t.TutorID, t.HourlyRate 
    FROM TUTOR t 
    JOIN USER u ON t.TutorID = u.UserID 
    WHERE t.TutorID = $tutor_id";
  $res_tutor_fetch = $conn->query($sql_tutorfetch);
  $tutor = $res_tutor_fetch ? $res_tutor_fetch->fetch_assoc() : null;

  if ($tutor) {
    if (!empty($tutor['Title'])) {
      $t_name = $tutor['Title'] . " " . $tutor['Name'];
    } else {
      $t_name = $tutor['Name'];
    }
    ?>
  <div class='card'>
  <h3>Book Lesson with <?= $t_name ?></h3>
  <p>Hourly Rate: $<?= number_format($tutor['HourlyRate'], 2) ?> / hour</p>
  <form method='POST' action='?page=bookings'>
    <input type='hidden' name='action' value='create_booking'>
    <input type='hidden' name='tutor_id' value='<?= $tutor['TutorID'] ?>'>
    <div class='form-grid'>
      <div>
        <label>Lesson Date:</label>
        <input type='date' name='lesson_date' required min='<?= date('Y-m-d') ?>'>
      </div>
      <div>
        <label>Start Time:</label>
        <input type='time' name='lesson_time' required>
      </div>
      <div>
        <label>Duration:</label>
        <select name='duration'>
          <option value='0.5'>0.5 Hour (30 Mins)</option>
          <option value='1.0' selected>1.0 Hour (60 Mins)</option>
          <option value='1.5'>1.5 Hours (90 Mins)</option>
          <option value='2.0'>2.0 Hours (120 Mins)</option>
          <option value='2.5'>2.5 Hours (150 Mins)</option>
          <option value='3.0'>3.0 Hours (180 Mins)</option>
          <option value='3.5'>3.5 Hours (210 Mins)</option>
          <option value='4.0'>4.0 Hours (240 Mins)</option>
        </select>
      </div>
    </div><br>
    <button type='submit' class='btn-primary'>CONFIRM BOOKING</button> 
    <a href='?page=bookings' class='btn-sec'>Cancel</a>
  </form>
  </div>
  <?php
  }
}

$sql_bookings_fetch = "
  SELECT b.BookingID, b.LessonDate, b.LessonTime, b.Duration, b.Status, u.Name AS TutorName, u.Title AS TutorTitle, t.HourlyRate, t.TutorID, r.Rating 
  FROM BOOKING b 
  JOIN USER u ON b.TutorID = u.UserID 
  JOIN TUTOR t ON b.TutorID = t.TutorID 
  LEFT JOIN REVIEW r ON b.BookingID = r.BookingID 
  WHERE b.StudentID = {$_SESSION['user_id']} 
  ORDER BY b.LessonDate DESC
";
$student_bookings = fetch_rows($conn->query($sql_bookings_fetch));
?>

<table class='data-table'>
  <thead>
  <tr>
  <th>Booking ID</th>
  <th>Tutor Name</th>
  <th>Date</th>
  <th>Time & Duration</th>
  <th>Total Fee</th>
  <th>Status</th>
  <th>Actions</th>
  </tr>
  </thead>
  <tbody>
  <?php if (count($student_bookings) == 0): ?>
  <tr><td colspan='7'>No bookings yet.</td></tr>
  <?php else: ?>
  <?php foreach ($student_bookings as $booking): ?>
    <?php
      $duration1 = (float)$booking['Duration'];
      $total_cost = $booking['HourlyRate'] * $duration1;
      $cost = number_format($total_cost, 2);
      $status_lower = strtolower($booking['Status']);

      if ($duration1 < 1.0) {
        $dur_label = ($duration1 * 60) . " mins";
      } else {
        $dur_label = $duration1 . " hrs";
      }
 
     if (!empty($booking['TutorTitle'])) {
        $tutor_name = $booking['TutorTitle'] . " " . $booking['TutorName'];
      } else {
        $tutor_name = $booking['TutorName'];
      }
    ?>
  <tr>
    <td>#<?= $booking['BookingID'] ?></td>
    <td><?= $tutor_name ?></td>
    <td><?= $booking['LessonDate'] ?></td>
    <td><?= substr($booking['LessonTime'], 0, 5) ?> (<?= $dur_label ?>)</td>
    <td>$<?= $cost ?></td>
    <td><span class='status-tag status-<?= $status_lower ?>'><?= $booking['Status'] ?></span></td>
    <td>
      <?php if ($booking['Status'] == 'Pending' || $booking['Status'] == 'Confirmed'): ?>
        <div style='display:flex; gap:0.25rem; margin-bottom:0.25rem;'>
          <?php if ($booking['Status'] == 'Confirmed'): ?>
            <form method='POST' action='?page=bookings'>
              <input type='hidden' name='action' value='update_booking_status'>
              <input type='hidden' name='booking_id' value='<?= $booking['BookingID'] ?>'>
              <input type='hidden' name='new_status' value='Completed'>
              <button type='submit' class='btn-success'>COMPLETED</button>
            </form>
          <?php endif; ?>
          <form method='POST' action='?page=bookings'>
            <input type='hidden' name='action' value='update_booking_status'>
            <input type='hidden' name='booking_id' value='<?= $booking['BookingID'] ?>'>
            <input type='hidden' name='new_status' value='Cancelled'>
            <button type='submit' class='btn-danger'>CANCEL</button>
          </form>
        </div>
      <?php endif; ?>

      <?php if ($booking['Status'] == 'Completed' && $booking['Rating'] === null): ?>
        <form method='POST' action='?page=bookings' style='display:flex; gap:0.25rem;'>
          <input type='hidden' name='action' value='submit_review'>
          <input type='hidden' name='booking_id' value='<?= $booking['BookingID'] ?>'>
          <input type='hidden' name='tutor_id' value='<?= $booking['TutorID'] ?>'>
          <select name='rating' style='width:70px;'>
            <option value='5'> 5★ </option>
            <option value='4'> 4★ </option>
            <option value='3'> 3★ </option>
            <option value='2'> 2★ </option>
            <option value='1'> 1★ </option>
          </select>
          <button type='submit' class='btn-primary' style='padding:0.2rem 0.5rem;'>SUBMIT</button>
        </form>
      <?php elseif ($booking['Rating'] !== null): ?>
        ★ <?= $booking['Rating'] ?> / 5
      <?php elseif ($booking['Status'] == 'Cancelled'): ?>
        <em>Lesson Cancelled</em>
      <?php elseif ($booking['Status'] == 'Pending'): ?>
        <em>Awaiting Tutor Confirmation</em>
      <?php endif; ?>
    </td>
  </tr>
<?php endforeach; ?>
<?php endif; ?>
  </tbody>
</table>