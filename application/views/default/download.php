<?php
// global variable
global $defaultUser;

// stylesheet
$pages_content = "<style>@page { margin: 5px; } body { margin: 5px; } .page_break { page-break-before: always; } div.page_break+div.page_break { page-break-before: always; }</style>";

// set the site root
$site_root = config_item("site_root");

// require the autoload file
require "{$site_root}system/libraries/dompdf/vendor/autoload.php";

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

// set the logo of the client account
$orientation = "landscape";
$pages_content .= "";

$file_name = "Test Document";

// check if the file to download has been parsed
if((isset($_GET["file"]) && !empty($_GET["file"])) || (isset($_GET["file_id"], $_GET["file_uid"]))) {
    
    // if the file was parsed in the query parameter
    if(isset($_GET["file"])) {
        // get the file name
        $file_to_download = xss_clean(base64_decode($_GET["file"]));

        // explode the text
        $name = explode($myClass->underscores, $file_to_download);

        // if no record id is parsed
        if(!isset($name[1]) || empty(($name[1]))) {
            print "Access Denied!";
            return;
        }
        // continue processing
        $record_id = $name[1];
    } else {
        // set the record id
        $record_id = xss_clean($_GET["file_id"]);
    }

    // If the user wants to download a note
    if(isset($_GET["file_uid"]) && ($_GET["file_uid"] == "note")) {
        // item id
        $item_id = $name[0];
        // get the record from the database
        $attachment_record =  $myClass->columnValue(
            "a.title, a.content, a.company_id, a.tags, a.date_created", 
            "companies_notes a", "a.item_id='{$item_id}' AND a.company_id='{$record_id}'"
        );
        // if no record found
        if(empty($attachment_record)) {
            print "Access Denied!";
            return;
        }

        print "Feature coming soon!";

        return;
    } else {
        // get the record information
        $attachment_record =  $myClass->columnValue("resource, resource_id, description", "files_attachment", "record_id='{$record_id}'");
        
        // if no record found
        if(empty($attachment_record)) {
            print "Access Denied!";
            return;
        }

        // set the file to download
        $file_to_download = isset($_GET["file_uid"]) ? xss_clean($_GET["file_uid"]) : $name[0];

        // if the file_uid was parsed then get the file name
        if(isset($_GET["file_uid"])) {

            // convert the string into an object
            $file_list = json_decode($attachment_record->description);

            // found
            $found = false;

            // loop through each file
            foreach($file_list->files as $key => $eachFile) {
                
                // check if the id matches what has been parsed in the url
                if($eachFile->unique_id == $file_to_download) {
                    $file_to_download = $eachFile->path;
                    $found = true;
                    break;
                }
            }

            if(!$found) {
                print "Access Denied!";
                return;
            }
        }

        // confirm that the file really exists
        if(is_file($file_to_download) && file_exists($file_to_download)) {
            // force the file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_to_download));
            readfile($file_to_download);
            exit;
        }
    }
    
}

/** Download Timetables */
elseif(confirm_url_id(1, "timetable") && isset($_GET["tb_id"])) {

    // set the timetable id
    $timetable_id = xss_clean($_GET["tb_id"]);
    $codeOnly = (bool) isset($_GET["code_only"]);
    $file_name = "timetable_calendar.pdf";
        
    // set some parameters
    $param = (object) ["data" => [], "timetable_id" => $timetable_id, "code_only" => $codeOnly, "download" => true];
    $param->client_data = $defaultUser->client ?? null;

    // create a new object
    $timetableClass = load_class("timetable", "controllers", $param);

    // set a day parameter
    if(isset($_GET["load"]) && (in_array($_GET["load"], ["yesterday", "today", "tomorrow"]))) {
        $param->today_only = xss_clean($_GET["load"]);
    }
    
    // load the table
    $content = $timetableClass->draw($param);
    
    // end query if no result found
    if(!isset($content["result"])) {
        print_r($content);
        return;
    }

    if(!isset($_GET["dw"])) {
        print_r($content["table"]);
        return;
    }

    $pages_content .= $content["table"];
}

/** Download Payslips */
elseif(confirm_url_id(1, "payslip") && isset($_GET["pay_id"])) {

    $payslip_id = xss_clean($_GET["pay_id"]);
    $param = (object) ["payslip_id" => $payslip_id, "download" => true, "clientId" => $session->clientId];
    $param->client_data = $defaultUser->client ?? null;

    // load the table
    $content = load_class("payroll", "controllers", $param)->draw($param);
    $file_name = "Employee_Payslip.pdf";
    $orientation = "L";

    // end query if no result found
    if(!isset($_GET["dw"])) {
        print $content["data"];
        print '<script>
                // window.onload = (evt) => { window.print(); }
                // window.onafterprint = (evt) => { window.close(); }
            </script>';
        return;
    }
    $pages_content .= $content["data"];
}

/** Download Receipt ID */
elseif(confirm_url_id(1, "receipt") && isset($_GET["rpt_id"])) {

    $receipt_id = xss_clean($_GET["rpt_id"]);
    $param = (object) ["userData" => $defaultUser, "item_id" => $receipt_id, "download" => true, "clientId" => $session->clientId];
    $param->client_data = $defaultUser->client ?? null;

    // create new object
    $feesObject = load_class("fees", "controllers", $param);

    // load the table
    $content = $feesObject->list($param);
    $file_name = "Receipt_Download.pdf";
    $receipt = "";
    $orientation = "portrait";

    // end query if no result found
    if(is_array($content)) {

        // create a new object
        $param = (object) [
            "getObject" => [],
            "download" => true,
            "data" => $content["data"],
            "receipt_id" => $receipt_id,
            "clientId" => $session->clientId
        ];
        // load the receipt 
        $pages_content .= $feesObject->receipt($param);
    }

}

/** Download Course Material (Units / Lessons) */
elseif(confirm_url_id(1, "coursematerial")) {
    
    // assign the course variable
    $course = base64_decode(xss_clean($_GET["cs_mat"]));
    $course = explode("_", $course);

    // continue
    if(isset($course[2])) {

        // get the parameters
        $params = (object) [
            "limit" => 1,
            "full_details" => true,
            "course_id" => $course[1],
            "clientId" => $course[2],
            "client_data" => $defaultUser->client ?? null
        ];

        // create new object
        $courseObj = load_class("courses", "controllers", $params);
        $course_info = $courseObj->list($params);

        // if data was found
        if(isset($course_info["data"][0])) {

            // get the information
            $orientation = "portrait";
            $course_info = $course_info["data"][0];
            $pages_content .= $courseObj->draw($course_info);
            
            // file name
            $file_name = "Course_Material.pdf";

            if(!isset($_GET["dw"])) {
                print $content;
            }
        }
    }
}

/** Download Timetables */
elseif(confirm_url_id(1, "attendance")) {
    
    /** Start processing */
    $getObject = (array) $_GET;
    $getObject = (object) array_map("xss_clean", $getObject);
    
    // end the query
    if(empty($defaultUser->client)) {
        print "Access Denied!";
        return;
    }

    // set some parameters
    $getObject->download = true;
    $getObject->client_data = $defaultUser->client;
    $getObject->user_type = $getObject->user_type ?? null;
    $getObject->clientId = $defaultUser->client->client_id;
    $getObject->academic_year = $defaultUser->client->client_preferences->academics->academic_year;
    $getObject->academic_term = $defaultUser->client->client_preferences->academics->academic_term;

    // confirm the user type
    if(($getObject->user_type === "student") && !isset($getObject->class_id)) {
        print "Access Denied!";
        return;
    }

    // create an object
    $orientation = "landscape";
    $file_name = "Attendance_Log";
    $attendanceObject = load_class("attendance", "controllers", $getObject);
    $pages_content .= $attendanceObject->attendance_report($getObject)["data"]["table_content"];
}

/** Download The Terminal Report */
elseif(confirm_url_id(1, "terminal") && isset($_GET["academic_term"], $_GET["academic_year"])) {
    // set the get parameters as the values
    $params = (object) $_GET;
    $params->client_data = $defaultUser->client ?? null;

    // set the class
    $reportObj = load_class("terminal_reports", "controllers", $params);

    // generate the report
    $data = $reportObj->generate($params);
    
    $start = 0;
    $count = count($data["data"]["sheets"]);

    // loop through the data
    foreach($data["data"]["sheets"] as $key => $info) {

        $start++;

        $pages_content .= $info["report"];
        $pages_content .= $count !== $start ? "\n<div class=\"page_break\"></div>" : null;
    }
}

/** Download An Incident Report */
elseif(confirm_url_id(1, "incident")) {
    // set the get parameters as the values
    $params = (object) $_GET;
    $params->limit = 1;
    $orientation = "portrait";
    $params->full_details = true;
    $params->client = $defaultUser->client;
    $params->userId = $defaultUser->user_id;
    $params->clientId = $defaultUser->client_id;
    $params->client_data = $defaultUser->client ?? null;

    // set the class
    $incidentObj = load_class("incidents", "controllers", $params);
    $incidentItem = $incidentObj->list($params)["data"];

    $pages_content .= $incidentObj->draw($params, $incidentItem);
}

/** Fees Report */
elseif(confirm_url_id(1, "fees")) {

    /** Start processing */
    $getObject = (array) $_GET;
    $getObject = (object) array_map("xss_clean", $getObject);

    // set the date range
    $date_range = "";
    $date_range .= isset($getObject->start_date) && !empty($getObject->start_date) ? $getObject->start_date : null;
    $date_range .= isset($getObject->end_date) && !empty($getObject->end_date) ? ":" . $getObject->end_date : null;

    // set the parameters
    $item_param = (object) [
        "category_id" => $getObject->category_id ?? null,
        "student_id" => $getObject->student_id ?? null,
        "item_id" => $getObject->receipt_id ?? null,
        "class_id" => $getObject->class_id ?? null,
        "client_data" => $defaultUser->client,
        "clientId" => $defaultUser->client_id,
        "date_range" => $date_range,
        "userData" => $defaultUser,
    ];

    // create a new object
    $feesObject = load_class("fees", "controllers", $item_param);

    // load the receipt data
    $data = $feesObject->list($item_param)["data"];

    // if the record was found
    if(is_array($data)) {

        // create a new object
        $param = (object) [
            "getObject" => $getObject,
            "data" => $data,
            "client" => $defaultUser->client,
            "download" => true,
            "isPDF" => true,
            "receipt_id" => $getObject->receipt_id ?? null,
            "clientId" => $defaultUser->client_id
        ];
        
        // load the receipt 
        $pages_content .= $feesObject->receipt($param);
    }

}

/** Account Statement Reports */
elseif(confirm_url_id(1, "accounting")) {
    // get the parameters
    $getObject = (object) $_GET;

    // set the parameters
    $orientation = (isset($getObject->display) && ($getObject->display == "notes"))  ? "landscape" : "portrait";
    $getObject->client_data = $defaultUser->client;
    $getObject->clientId = $defaultUser->client_id;

    $accountsObj = load_class("accounting", "controllers");
    $pages_content .= $accountsObj->statement($getObject);
}

/** Print Student Bill */
elseif(confirm_url_id(2) && confirm_url_id(1, "student_bill")) {

    // get the parameters
    $getObject = (array) $_GET;
    $getObject = (object) array_map("xss_clean", $getObject);

    // set the parameters
    $item_param = (object) [
        "userData" => $defaultUser,
        "student_id" => $SITEURL[2],
        "clientId" => $defaultUser->client_id,
        "client_data" => $defaultUser->client,
        "academic_year" => $getObject->academic_year ?? null,
        "academic_term" => $getObject->academic_term ?? null
    ];

    // if the user wants to print it out
    if(isset($getObject->print)) {
        $item_param->print = true;
    }

    // create a new object
    $feesObject = load_class("fees", "controllers", $item_param);

    $orientation = "P";
    $pages_content .= $feesObject->bill($item_param);

    // if the user wants to print it out
    if(isset($item_param->print)) {
        die($pages_content);
    }
}
// load the html content
$dompdf->loadHtml($pages_content);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper("A4", $orientation);

// Render the HTML as PDF
$dompdf->render();

// if the user wants to auto download
$download_file = (bool) isset($_GET["auto_download"]);

// Output the generated PDF to Browser
$dompdf->stream($file_name ?? null, ["compress" => 1, "Attachment" => $download_file]);

exit;