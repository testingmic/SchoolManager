<?php
// set the title
$page_title = "Dashboard";

// require the headtags
require "headtags.php";
?>
<?= pageoverlay(); ?>
<div class="main-content" id="pagecontent"></div>
<?php require "foottags.php"; ?>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="student_label">Student Label</label>
                    <input type="checkbox" name="labels[\'student_label\']" id="student_label" class="brands-checkbox">
                </div>
            </div>