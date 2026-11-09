<?php

function district_dropdown($name, $selected = '', $default = '-- Select District --') {
  global $hk_regions;
  ?>
  <select name="<?= $name ?>">
    <option value=""><?= $default ?></option>
    <?php foreach ($hk_regions as $region_name => $districts): ?>
        <optgroup label="<?= $region_name ?>">
          <?php foreach ($districts as $d): ?>
            <?php $sel = ($selected === $d) ? 'selected' : ''; ?>
            <option value="<?= $d ?>" <?= $sel ?>><?= $d ?></option>
          <?php endforeach; ?>
        </optgroup>
      <?php endforeach; ?>
  </select>
  <?php
}

// help sql errors

function fetch_rows(mysqli_result|bool $result): array {
    return ($result instanceof mysqli_result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function get_subjects($conn) {
  $all_subj = fetch_rows($conn->query("SELECT SubjectID, SubjectName, Category FROM SUBJECT ORDER BY SubjectID ASC"));
  
  $categorized = [];
  foreach ($all_subj as $subj) {
    $category = $subj['Category'];
    if (!isset($categorized[$category])) {
      $categorized[$category] = [];
    }
  $categorized[$category][] = $subj;
  }
  return $categorized;
}

function rating_level($rating) {
  if ($rating >= 4.0) {
    return 'rating-good';
  } else if ($rating >= 3.0) {
    return 'rating-okay';
  } else {
    return 'rating-bad';
  }
}

