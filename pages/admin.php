<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'verify_document') {
  if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin') {
    $doc_id = (int)$_POST['doc_id'];
    $new_status = $_POST['doc_status'];

    if ($new_status == 'Approved' || $new_status == 'Rejected') {
      $sql_doc = "UPDATE TUTOR_DOCUMENT SET Status = '$new_status' WHERE DocID = $doc_id";
        $conn->query($sql_doc);
        $alert_message = "<div class='alert-success'>Updated successfully!</div>";
      }
    }
  unset($_POST['action']);
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
  ?>
<div class='alert-error'>Access Denied: Administrators only.</div>
<?php
  return;
}

if (!empty($alert_message)) {
    echo $alert_message;
}
?>

<h2>Administrative Panel</h2>

<h3>Pending Verification</h3>
<?php
$docs = fetch_rows($conn->query("
    SELECT d.DocID, d.FilePath, d.OriginalName, d.Status, u.Name 
    FROM TUTOR_DOCUMENT d 
    JOIN USER u ON d.TutorID = u.UserID 
    ORDER BY d.UploadedAt DESC
"));
?>

<table class='data-table'>
  <thead>
  <tr>
    <th>Tutor</th>
    <th>Document</th>
    <th>Status</th>
    <th>Action</th>
  </tr>
  </thead>
  <tbody>
  <?php if (count($docs) == 0): ?>
    <tr><td colspan='4'>No verification documents submitted yet.</td></tr>
  <?php else: ?>
    <?php foreach ($docs as $doc): ?>
        <tr>
          <td><?= $doc['Name'] ?></td>
          <td><a href='<?= $doc['FilePath'] ?>' target='_blank'><?= $doc['OriginalName'] ?></a></td>
          <td><?= $doc['Status'] ?></td>
          <td>
            <?php if ($doc['Status'] == 'Pending'): ?>
              <form method='POST' action='?page=admin' style='display:inline;'>
                <input type='hidden' name='action' value='verify_document'>
                <input type='hidden' name='doc_id' value='<?= $doc['DocID'] ?>'>
                <button type='submit' name='doc_status' value='Approved' class='btn-success'>APPROVE</button> 
                <button type='submit' name='doc_status' value='Rejected' class='btn-danger'>REJECT</button>
              </form>
            <?php else: ?>
            <em>Reviewed</em>
            <?php endif; ?>
          </td>
      </tr>
  <?php endforeach; ?>
<?php endif; ?>
</tbody>
</table><br>

<?php
$users = fetch_rows($conn->query("SELECT * FROM USER ORDER BY UserID ASC"));

$bookings = fetch_rows($conn->query("SELECT * FROM BOOKING ORDER BY BookingID ASC"));
?>

<h3>Users</h3>
<table class='data-table'>
  <thead>
  <tr>
    <th>UserID (PK)</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>District</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($users as $user_row): ?>
    <?php
      $uname = !empty($user_row['Title']) ? $user_row['Title'] . " " . $user_row['Name'] : $user_row['Name'];
      $district = (isset($user_row['District']) && !empty($user_row['District'])) ? $user_row['District'] : 'N/A';
      ?>
    <tr>
      <td><strong><?= $user_row['UserID'] ?></strong></td>
      <td><?= $uname ?></td>
      <td><?= $user_row['Email'] ?></td>
      <td><?= $user_row['Role'] ?></td>
      <td><?= $district ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table><br>

<h3>Bookings</h3>
<table class='data-table'>
  <thead>
  <tr>
    <th>BookingID (PK)</th>
    <th>StudentID (FK)</th>
    <th>TutorID (FK)</th>
    <th>Lesson Date</th>
    <th>Duration (Hrs)</th>
    <th>Status</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($bookings as $booking_row): ?>
    <tr>
      <td><strong><?= $booking_row['BookingID'] ?></strong></td>
      <td>User #<?= $booking_row['StudentID'] ?></td>
      <td>Tutor #<?= $booking_row['TutorID'] ?></td>
      <td><?= $booking_row['LessonDate'] ?></td>
      <td><?= $booking_row['Duration'] ?> hr(s)</td>
       <td><?= $booking_row['Status'] ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>