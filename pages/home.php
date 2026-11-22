<?php
$total_districts = 0;
foreach ($hk_regions as $region => $districts) {
  $total_districts = $total_districts + count($districts);
}
$total_regions = count($hk_regions);

$total_subjects = 0;
foreach ($subjects as $category_name => $subjs) {
  $total_subjects = $total_subjects + count($subjs);
}
$total_categories = count($subjects);
?>

<div style="background-color: var(--nav-bg); color: var(--nav-text); padding: 2.5rem 2rem; border-radius: 6px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
  <p style="font-size: 0.9rem; color: #60a5fa; margin: 0 0 0.5rem 0; font-weight: bold; letter-spacing: 1px;">HKDSE Tutoring</p>
  <h1 style="font-size: 2.2rem; margin: 0 0 0.5rem 0; color: #ffffff; line-height: 1.2;">Private tutoring for Hong Kong secondary students</h1>
  <p style="font-size: 1.05rem; margin: 0 0 1.5rem 0; color: #cbd5e1; max-width: 720px;">Search tutors across <?= $total_districts ?> Hong Kong districts and <?= $total_subjects ?> subjects. Compare hourly rates and student ratings before you message a tutor or book a lesson.</p>
  <a href="?page=search" class="btn-primary" style="padding: 0.8rem 1.5rem; font-size: 1rem;">Find Tutors</a>

  <div style="margin-top: 2rem; display: flex; align-items: center; gap: 0.75rem;">
    <img src="logo.png" alt="TutorConnect HK logo" style="height: 32px; filter: brightness(0) invert(1);">
    <span style="font-size: 1.3rem; font-weight: bold; color: #ffffff;">TutorConnect<span style="color: #60a5fa;">.HK</span></span>
  </div>
</div>

<div class="card" style="padding: 2rem;">
  <h2 style="margin: 0 0 1rem 0;">What TutorConnect HK offers</h2>
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap: 1.5rem;">
    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
      <div style="margin-bottom: 0.5rem;">
        <span class="verified-badge">✔ Verified</span>
      </div>
      <h3 style="margin: 0 0 0.25rem 0; font-size: 1.05rem;">Verified tutors</h3>
      <p style="margin: 0; font-size: 0.9rem;">Administrators review uploaded qualification certificates before approving a verified badge.</p>
    </div>

    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
      <div style="margin-bottom: 0.5rem; display: flex; flex-wrap: wrap; gap: 0.4rem;">
        <span class="rating-badge rating-good" style="font-size: 0.8rem; padding: 0.2rem 0.5rem;">4.80</span>
        <span class="rating-badge rating-okay" style="font-size: 0.8rem; padding: 0.2rem 0.5rem;">3.50</span>
        <span class="rating-badge rating-bad" style="font-size: 0.8rem; padding: 0.2rem 0.5rem;">2.00</span>
      </div>
      <h3 style="margin: 0 0 0.25rem 0; font-size: 1.05rem;">Student ratings</h3>
      <p style="margin: 0; font-size: 0.9rem;">Students and parents submit a rating from 1 to 5 stars after each completed lesson. </p>
    </div>

    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
      <div style="margin-bottom: 0.5rem; display: flex; flex-wrap: wrap; gap: 0.4rem;">
        <span class="msg-bubble msg-received" style="font-size: 0.8rem; padding: 0.2rem 0.55rem;">Friday at 4 PM?</span>
        <span class="msg-bubble msg-sent" style="font-size: 0.8rem; padding: 0.2rem 0.55rem;">Confirmed.</span>
      </div>
      <h3 style="margin: 0 0 0.25rem 0; font-size: 1.05rem;">Direct messaging</h3>
      <p style="margin: 0; font-size: 0.9rem;">Users send text messages to tutors on the platform before and after booking.</p>
    </div>

    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
      <div style="margin-bottom: 0.5rem;">
        <span class="status-tag status-completed" style="font-size: 0.8rem; padding: 0.25rem 0.55rem;">$250.00 / hour</span>
      </div>
      <h3 style="margin: 0 0 0.25rem 0; font-size: 1.05rem;">Upfront hourly rates</h3>
      <p style="margin: 0; font-size: 0.9rem;">Every tutor profile displays an hourly rate in Hong Kong dollars.</p>
    </div>
  </div>
</div>

<div class="card" style="padding: 2rem;">
  <h2 style="margin-top: 0;">How TutorConnect HK connects students and tutors</h2>
  <p>Students and parents filter tutors by keyword, subject, district, maximum hourly rate, and verification status. Each search result displays the tutor's qualifications, district, hourly rate, and average rating.</p>
  <p>The booking system checks existing lesson times the database so a student or tutor cannot book two lessons at the same time. </p>
</div>

<div class="card" style="padding: 2rem;">
  <h2 style="margin-top: 0;">Available subjects</h2>
  <p style="margin-bottom: 1rem;">Tutors teach <?= $total_subjects ?> DSE subjects across <?= $total_categories ?> categories. Select any subject on the search page to filter matching tutors.</p>

  <?php foreach ($subjects as $category_name => $subjs): ?>
    <h3 style="font-size: 0.95rem; margin: 1rem 0 0.5rem 0; border-left: 3px solid var(--primary-color); padding-left: 0.5rem;"><?= $category_name ?></h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem;">
      <?php foreach ($subjs as $s): ?>
        <div style="background-color: var(--contact-bg); padding: 0.5rem 0.7rem; border-radius: 4px; border: 1px solid var(--border-color); font-size: 0.85rem; text-align: center;">
          <?= $s['SubjectName'] ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>

<div class="card" style="padding: 2rem;">
  <h2 style="margin-top: 0;">How it works</h2>
  <div class="form-grid" style="gap: 1rem; margin-top: 1rem;">
    <div style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;">
      <div style="font-size: 1.4rem; font-weight: bold; color: var(--primary-color);">1</div>
      <strong>Create an account</strong>
      <p style="margin: 0.25rem 0 0 0; font-size: 0.9rem;">Register a Student, Parent, or Tutor account. Tutors upload qualification certificates for administrator review.</p>
    </div>
    <div style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;">
      <div style="font-size: 1.4rem; font-weight: bold; color: var(--primary-color);">2</div>
      <strong>Search for a tutor</strong>
      <p style="margin: 0.25rem 0 0 0; font-size: 0.9rem;">Students and parents filter tutors by keyword, subject, maximum hourly rate, or any of the <?= $total_districts ?> Hong Kong districts.</p>
    </div>
    <div style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;">
      <div style="font-size: 1.4rem; font-weight: bold; color: var(--primary-color);">3</div>
      <strong>Book a lesson</strong>
      <p style="margin: 0.25rem 0 0 0; font-size: 0.9rem;">Select a lesson date, a start time, and a duration from 0.5 to 4 hours. The system multiplies the tutor's hourly rate by the duration to set the total fee.</p>
    </div>
    <div style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;">
      <div style="font-size: 1.4rem; font-weight: bold; color: var(--primary-color);">4</div>
      <strong>Learn and review</strong>
      <p style="margin: 0.25rem 0 0 0; font-size: 0.9rem;">Send messages to your tutor to coordinate lesson details. After you mark a lesson as completed, submit a rating from 1 to 5 stars to update the tutor's average score.</p>
    </div>
  </div>
</div>

<div class="card" style="padding: 2rem;">
  <h2 style="margin-top: 0;">Register as a tutor</h2>
  <p>Tutors create a profile page with a biography, academic qualifications, an hourly rate, and a list of subjects. You upload a certificate in PDF, JPG, JPEG, or PNG format up to 5 megabytes to apply for the <span class="verified-badge">✔ Verified</span> badge. You accept or decline booking requests on your tutor dashboard and exchange messages with students and parents.</p>
  <a href="?page=register" class="btn-primary" style="padding: 0.6rem 1.2rem;">Create Tutor Account</a>
</div>

<div style="background-color: var(--nav-bg); color: var(--nav-text); padding: 2rem; border-radius: 6px; margin-bottom: 1.5rem; text-align: center; border: 2px solid var(--border-color);">
  <h2 style="margin: 0 0 0.5rem 0; color: #ffffff; font-size: 1.5rem;">Search tutors or create an account</h2>
  <p style="margin: 0 0 1rem 0; color: #cbd5e1;">Browse all <?= $total_subjects ?> subjects without signing in, or log in with a Student or Parent account to message tutors and book lessons.</p>
  <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
    <a href="?page=search" class="btn-primary" style="padding: 0.8rem 1.5rem;">Find Tutors</a>
    <a href="?page=register" class="btn-sec" style="padding: 0.8rem 1.5rem;">Create Account</a>
  </div>
</div>