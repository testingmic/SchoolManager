<?php
// global variable
global $defaultUser, $isSupport, $usersClass, $defaultClientData;

// stylesheet
$pages_content = "<style>@page { margin: 5px; } body { margin: 5px; } .page_break { page-break-before: always; } div.page_break+div.page_break { page-break-before: always; }</style>";

// set no memory limit
error_reporting(0);
ini_set("memory_limit", "-1");

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

// set the logo of the client account
$orientation = "landscape";
$pages_content .= "";

$file_name = "Test Document";
$fileType = "A4";

// run this section if not a support user
if(!$isSupport) {
    
    // set the params
    $param = (object) [
        "clientId" => $defaultUser->client_id,
        "client_data" => $defaultUser->client
    ];

    /** check if the file to download has been parsed */
    if((isset($_GET["file"]) && !empty($_GET["file"])) || (isset($_GET["file_id"], $_GET["file_uid"]))) {
        
        // if the file was parsed in the query parameter
        if(isset($_GET["file"])) {
            // get the file name
            $file_to_download = xss_clean(base64_decode($_GET["file"]));

            // explode the text
            $name = explode($myClass->underscores, $file_to_download);

            // if no record id is parsed
            if(!isset($name[1]) || empty(($name[1]))) {
                print "File not found.";
                return;
            }
            // continue processing
            $record_id = $name[1];
        } else {
            // set the record id
            $record_id = xss_clean($_GET["file_id"]);
        }

        // get the record information
        $attachment_record = $myClass->columnValue("resource, client_id, resource_id, description", "files_attachment", "record_id='{$record_id}'");
        
        // if no record found
        if(empty($attachment_record)) {
            print "File not found.";
            return;
        }

        // set the file to download
        $file_to_download = isset($_GET["file_uid"]) ? xss_clean($_GET["file_uid"]) : $name[0];

        // if the file_uid was parsed then get the file name
        if(isset($_GET["file_uid"])) {

            // convert the string into an object
            $file_ref_id = xss_clean($_GET["file_uid"]);
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
                print "File not found.";
                return;
            }
            
            // if the reference is documents
            if(isset($_GET["ref"]) && ($_GET["ref"] === "docs")) {
                // update the file downloads count
                $myClass->_save("documents", 
                    ["column_value" => "downloads_count=(downloads_count+1)", "last_updated" => "now()"], 
                    ["file_ref_id" => $file_ref_id, "client_id" => $attachment_record->client_id]
                );
            }
        }

        // confirm that the file really exists
        if(is_file($file_to_download) && file_exists($file_to_download)) {
            if(!isset($_GET["preview"])) {
                // force the file download
                $myClass->force_download($file_to_download);
            } else {
                $file = "{$myClass->baseUrl}{$file_to_download}";
                die("<style>html, body { margin: 0; padding: 0; height: 100%; }</style><iframe style='width: 100%; height: 100%; border: none;' src='{$file}'></iframe>");
            }
        }
    
        
    }

    elseif(isset($_GET["qrcode"])) {
        $file_to_download = ROOT_DIRECTORY . "/assets/uploads/qrcodes/{$_GET["qrcode"]}";
        $myClass->force_download($file_to_download, $_GET['filename'] ?? 'QR Code');
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
            print print_page_content();
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
            $courseObj = load_class("subjects", "controllers", $params);
            $course_info = $courseObj->list($params);

            // if data was found
            if(isset($course_info["data"][0])) {

                // get the information
                $orientation = "portrait";
                $course_info = $course_info["data"][0];
                $pages_content .= $courseObj->draw($course_info);

                // file name
                $file_name = "Course_Material.".strtoupper($course_info->name).".pdf";

                if(!isset($_GET["dw"])) {
                    print "<div style='padding: 15px;'>";
                    print $pages_content;
                    print "</div>";
                    print print_page_content();
                    exit;
                }
            }
        }
    }

    /** Download Timetables */
    elseif(confirm_url_id(1, "attendance")) {
        /** Start processing */
        $getObject = $_GET;
        $getObject = (object) array_map("xss_clean", $getObject);
        
        // end the query
        if(empty($defaultUser->client)) {
            print "Access Denied!";
            return;
        }

        // set some parameters
        $getObject->download = true;
        $getObject->client_data = $defaultUser->client;
        $getObject->user_id = $getObject->user_id ?? null;
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
        $file_name = "Attendance_Log.pdf";
        $attendanceObject = load_class("attendance", "controllers", $getObject);
        $pages_content .= $attendanceObject->report($getObject)["data"]["table_content"];

    }

    /** Download The Terminal Report */
    elseif(confirm_url_id(1, "terminal") && isset($_GET["academic_term"], $_GET["academic_year"])) {
        // set the get parameters as the values
        $params = (object) $_GET;
        $params->client_data = $defaultUser->client ?? null;

        // set the class
        $file_name = "Terminal_Report.pdf";
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
        $file_name = "Incident_Log.pdf";
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

        // set the date range
        if(!empty($getObject->date_range)) {
            $break = $myClass->dateRange($getObject->date_range, "rparts");
            $getObject->start_date = $break["start_date"];
            $getObject->end_date = $break["end_date"];
            $date_range = $getObject->date_range;
        }

        // set the parameters
        $item_param = (object) [
            "order_by" => "ORDER BY a.id ASC",
            "group_by" => "GROUP BY a.payment_id",
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
        $file_name = "Fees_Log.pdf";
        $feesObject = load_class("fees", "controllers", $item_param);

        // load the receipt data
        $data = $feesObject->list($item_param)["data"];

        // if the record was found
        if(is_array($data)) {

            // create a new object
            $param = (object) [
                "getObject" => $getObject,
                "data" => $data,
                "end_date" => $getObject->end_date,
                "start_date" => $getObject->start_date,
                "client" => $defaultUser->client,
                "download" => true,
                "class_id" => $getObject->class_id ?? null,
                "category_id" => $getObject->category_id ?? null,
                "isPDF" => true,
                "receipt_id" => $getObject->receipt_id ?? null,
                "clientId" => $defaultUser->client_id
            ];
            
            // load the receipt 
            $pages_content .= $feesObject->receipt($param, "bold;font-size:20px");
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

        // set the file name
        $file_name = "Accounting_Report.pdf";

        $accountsObj = load_class("accounting", "controllers");
        $pages_content .= $accountsObj->statement($getObject);
    }

    /** Print Student Bill */
    elseif(confirm_url_id(1, "user")) {

        // get the parameters
        $getObject = (array) $_GET;
        $getObject = (object) array_map("xss_clean", $getObject);

        // set the parameters
        $item_param = (object) [
            "userData" => $defaultUser,
            "user_id" => $SITEURL[2] ?? null,
            "clientId" => $defaultUser->client_id,
            "client_data" => $defaultUser->client
        ];

        // if the user wants to print it out
        if(isset($getObject->print)) {
            $item_param->print = true;
        } else {
            $item_param->isPDF = true;
        }

        // create a new object
        $usersObject = load_class("users", "controllers", $item_param);

        $orientation = "P";
        $user_record = $usersObject->generate_user_record($item_param);
        $file_name = ($user_record['filename'] ?? 'User_Record');
        $pages_content .= $user_record["record"] ?? "Sorry! No record was found.";

        // if the user wants to print it out
        if(isset($item_param->print) || empty($user_record["record"])) {
            die($pages_content);
        }
    }

    /** Print Student Bill */
    elseif(confirm_url_id(1, "student_bill")) {

        // get the parameters
        $getObject = (array) $_GET;
        $getObject = (object) array_map("xss_clean", $getObject);

        // set the parameters
        $item_param = (object) [
            "userData" => $defaultUser,
            "student_id" => $SITEURL[2] ?? null,
            "clientId" => $defaultUser->client_id,
            "client_data" => $defaultUser->client,
            "class_id" => $getObject->class_id ?? null,
            "current_bal" => $getObject->current_bal ?? null,
            "student_ids" => $getObject->student_ids ?? null,
            "academic_year" => $getObject->academic_year ?? null,
            "academic_term" => $getObject->academic_term ?? null
        ];

        // if the user wants to print it out
        if(isset($getObject->print)) {
            $item_param->print = true;
        } else {
            $item_param->isPDF = true;
        }

        // create a new object
        $feesObject = load_class("fees", "controllers", $item_param);

        $orientation = "P";
        $file_name = "Student_Bill.pdf";
        $bill_record = $feesObject->bill($item_param);
        $pages_content .= is_array($bill_record) ? $bill_record["student_bill"] : $bill_record;

        // if the user wants to print it out
        if(isset($item_param->print)) {
            die($pages_content);
        }
    }

    /** Export Staff / Student Information */
    elseif(confirm_url_id(1, "export")) {
        // if no record id is parsed
        if(!confirm_url_id(3)) {
            print "Access Denied!";
            return;
        }
        // url item
        $item = $SITEURL[2];
        $user_id = $SITEURL[3];
        $orientation = "portrait";
        $clientId = $SITEURL[4] ?? null;

        // items to query
        if(!in_array($item, ["users"])) {
            print "Access Denied!";
            return;
        }
        // set the params
        $param = (object) [
            "user_id" => $user_id,
            "clientId" => $clientId,
            "client_data" => $defaultClientData
        ];
        // get the user data to export
        $file_name = "Export_Student_Data.pdf";
        $pages_content = $usersClass->export($param);
    }

    elseif(confirm_url_id(1, "idcard")) {
        $file_name = "ID_Card.pdf";
        $param->download_list = true;

        $orientation = "l";

        // set the parameters
        foreach(['card_preview_id', 'class_id', 'user_type'] as $key) {
            $param->{$key} = $_GET[$key] ?? null;
        }

        // stylesheet
        $pages_content = "<style>@page { margin: 0px; padding: 0px; } body { margin: 0px; padding: 0px; } .page_break { page-break-before: always; } div.page_break+div.page_break { page-break-before: always; }</style>";

        // get the request content
        $requestContent = load_class("cards", "controllers")->preview($param);
        $pages_content .= $requestContent["data"]["cards_list"] ?? "No cards found.";

        $fileType = array(0, 0, 375, 240); 

        if(isset($_GET["dw"])) {
            print_r($pages_content);
            exit;
        }

    }

}

// load the html content
$dompdf->loadHtml($pages_content);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper($fileType, $orientation);

// Render the HTML as PDF
$dompdf->render();

// if the user wants to auto download
$download_file = (bool) isset($_GET["auto_download"]);

// Output the generated PDF to Browser
$dompdf->stream($file_name ?? null, ["compress" => 1, "Attachment" => $download_file]);

exit;