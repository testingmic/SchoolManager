<?php
$filePath = $_GET['file'] ?? null;
if(empty($filePath)) {
  die("File path is required");
}
?>
<iframe 
  src="<?= $filePath ?>" 
  width="100%" 
  height="100%" 
  loading="lazy" 
  allowfullscreen 
  frameborder="0"
  style="border: none; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);"
></iframe>