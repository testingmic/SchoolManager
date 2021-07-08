<?php
// global variable
global $defaultUser;

// if a first parameter was parsed then end
if(confirm_url_id(1)) {
    print "Access Denied!";
    return;
}

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
elseif(isset($_GET["tb"]) && ($_GET["tb"] === "true") && isset($_GET["tb_id"])) {

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
elseif(isset($_GET["pay_id"]) && !isset($_GET["cs_mat"])) {

    $payslip_id = xss_clean($_GET["pay_id"]);
    $param = (object) ["payslip_id" => $payslip_id, "download" => true, "clientId" => $session->clientId];
    $param->client_data = $defaultUser->client ?? null;

    // load the table
    $content = load_class("payroll", "controllers", $param)->draw($param);
    $file_name = "Employee_Payslip.pdf";
    $orientation = "portrait";

    // end query if no result found
    if(!isset($_GET["dw"])) {
        print $content["data"];
        print '<script>
                window.onload = (evt) => { window.print(); }
                window.onafterprint = (evt) => { window.close(); }
            </script>';
        return;
    }
    $pages_content .= $content["data"];
}

/** Download Receipt ID */
elseif(isset($_GET["rpt_id"]) && !isset($_GET["pay_id"])) {

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
elseif(isset($_GET["cs_mat"]) && !isset($_GET["pay_id"]) && !isset($_GET["tb_id"])) {
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
elseif(isset($_GET["att_d"]) && ($_GET["att_d"] === "true") && isset($_GET["user_type"])) {
    
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
elseif(isset($_GET["terminal"], $_GET["academic_term"], $_GET["academic_year"]) && !isset($_GET["tb_id"])) {
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
elseif(isset($_GET["incident"]) && !isset($_GET["terminal"]) && !isset($_GET["tb_id"])) {
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

    if(isset($params->show)) {
        print $pages_content; exit;
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