<?php
$filePath = $_GET['file'] ?? null;
if(empty($filePath)) {
  die("File path is required");
}
?>
<iframe src="<?= $filePath ?>" width="100%" height="100%" frameborder="0"></iframe>