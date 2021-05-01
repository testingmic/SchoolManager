<?php
// set the get parameters as the values
$params = (object) $_GET;
$params->client_data = $defaultUser->client ?? null;

// set the class
$reportObj = load_class("terminal_reports", "controllers", $params);

// generate the report
$report = $reportObj->generate($params);

// loop through the results set and print the student result sheet
show_content("Terminal Report", "terminal_report.pdf", $report["data"]["sheets"], "landscape", $reportObj);
?>