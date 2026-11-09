
<?php

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Tutor') {
    ?>
  <div class='alert-error'>Access Denied.</div>
  <?php
    return;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_booking_status') {
  $booking_id    = (int)$_POST['booking_id'];
  $target_status = $_POST['new_status'];
  $current_user_id = $_SESSION['user_id'];
  $current_role    = $_SESSION['user_role'];

  $sql_fetch_booking = "SELECT * FROM BOOKING WHERE BookingID = $booking_id";
  $res_booking = $conn->query($sql_fetch_booking);
  $booking_data = $res_booking ? $res_booking->fetch_assoc() : null;

  if ($booking_data) {
    $allow_transition = false;

    if ($current_role == 'Tutor' && $booking_data['TutorID'] == $current_user_id) {
      if ($booking_data['Status'] == 'Pending' &&
        ($target_status == 'Confirmed' || $target_status == 'Cancelled')) {
        $allow_transition = true;
      }
    }

    if ($allow_transition) {
      $sql_up_status = "UPDATE BOOKING SET Status = '$target_status' WHERE BookingID = $booking_id";
      $conn->query($sql_up_status);
      $alert_message = "<div class='alert-success'>Updated successfully!</div>";
    } else {
      $alert_message = "<div class='alert-error'>No permission.</div>";
    }
  }
}
if (!empty($alert_message)) {
    echo $alert_message;
}

?>

<h2>My Booking Schedule</h2>
<?php
$sql_tutor_bookings = "
    SELECT b.BookingID, b.LessonDate, b.LessonTime, b.Duration, b.Status, u.Name AS StudentName 
    FROM BOOKING b 
    JOIN USER u ON b.StudentID = u.UserID 
    WHERE b.TutorID = {$_SESSION['user_id']} 
    ORDER BY b.LessonDate DESC
";
$tutor_bookings = fetch_rows($conn->query($sql_tutor_bookings));
?>

<table class='data-table'>
  <thead>
  <tr>
    <th>Booking ID</th>
    <th>Student Name</th>
    <th>Date</th>
    <th>Time</th>
    <th>Duration</th>
    <th>Status</th>
    <th>Actions</th>
  </tr>
  </thead>
  <tbody>
  <?php if (count($tutor_bookings) == 0): ?>
    <tr><td colspan='7'>No student bookings assigned.</td></tr>
  <?php else: ?>
    <?php foreach ($tutor_bookings as $tb): ?>
        <?php
                $status = strtolower($tb['Status']);
                $duration1 = (float)$tb['Duration'];
                $dur_label = ($duration1 < 1.0) ? ($duration1 * 60) . " mins" : $duration1 . " hrs";
                ?>
        <tr>
            <td>#<?= $tb['BookingID'] ?></td>
            <td><?= $tb['StudentName'] ?></td>
            <td><?= $tb['LessonDate'] ?></td>
            <td><?= substr($tb['LessonTime'], 0, 5) ?></td>
            <td><?= $dur_label ?></td>
            <td><span class='status-tag status-<?= $status ?>'><?= $tb['Status'] ?></span></td>
            <td>
                <?php if ($tb['Status'] == 'Pending'): ?>
                    <div style='display:flex; gap:0.25rem;'>
                        <form method='POST' action='?page=tutor_dashboardd'>
                            <input type='hidden' name='action' value='update_booking_status'>
                            <input type='hidden' name='booking_id' value='<?= $tb['BookingID'] ?>'>
                            <input type='hidden' name='new_status' value='Confirmed'>
                            <button type='submit' class='btn-success'>CONFIRM</button>
                        </form>

                        <form method='POST' action='?page=tutor_dashboardd'>
                            <input type='hidden' name='action' value='update_booking_status'>
                            <input type='hidden' name='booking_id' value='<?= $tb['BookingID'] ?>'>
                            <input type='hidden' name='new_status' value='Cancelled'>
                            <button type='submit' class='btn-danger'>DECLINE</button>
                        </form>
                    </div>
                <?php else: ?>
                    <em>N/A</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>