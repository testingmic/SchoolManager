<?php
$reportObj = load_class("terminal_reports", "controllers");
$params = (object) $_GET;
$report = $reportObj->generate($params);

// loop through the results set and print the student result sheet
foreach($report["data"]["sheets"] as $key => $value) {
    show_content("Terminal Report", "terminal_report.pdf", $value, "L", true);
    break;
}
?>