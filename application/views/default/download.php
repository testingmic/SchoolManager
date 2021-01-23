<?php
// if a first parameter was parsed then end
if(confirm_url_id(1)) {
    print "Access Denied!";
    return;
}

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
/** Download timetables */
elseif(isset($_GET["tb"]) && ($_GET["tb"] === "true") && isset($_GET["tb_id"])) {
    // set the timetable id
    $timetable_id = xss_clean($_GET["tb_id"]);
    $codeOnly = (bool) isset($_GET["code_only"]);

    // load the timetable information
    $params = (object) [
        "limit" => 1,
        "full_detail" => true,
        "timetable_id" => $timetable_id,
        "clientId" => $session->clientId,
    ];
    $data = load_class("timetable", "controllers")->list($params)["data"];

    // make the request
    if(empty($data)) {
        print "Access Denied!";
        return; 
    }
    $data = $data[$timetable_id];
    
    // create a test function
    function add_time($start_time, $interval) {
        $time = date("h:i A", strtotime("{$start_time} + {$interval}minutes"));
        return $time;
    }

    // column with calculation
    $slots = $data->slots;
    $width = round(100/($slots+1));

    // start drawing the table
    $html_table = "<style>table tr td, table tr th {padding:10px;}</style>";
    $html_table .= "<table border='1' width='100%'>";
    $html_table .= "<tr style='background:#2f5e74e3;color:#fff'><th width='{$width}%'></th>";
    $start_time = $data->start_time;

    // generate the header
    for($i = 0; $i < $slots; $i++) {
        // set the start time
        $start_time = date("h:i A", strtotime($start_time));
        $end_time = add_time($start_time, $data->duration);

        // show the time
        $html_table .= "<th width='{$width}%'>";
        $html_table .= "
        <div align='center'>
            {$start_time}<br>-<br>
            {$end_time}
        </div>";
        $html_table .= "</th>";
        $start_time = $end_time;
    }

    // days of the week
    $d_style = "style='background:rgba(235,249,163,0.9) padding-box !important;font-weight:bold;text-align:center;box-shadow:0 0 25px rgba(207,229,84,0.9) inset !important'";
    $days = ["Monday", "Tuesday", "Wednesday", "Thurday", "Friday", "Saturday", "Sunday"];
    $colors = ["#007bff", "#6610f2", "#6f42c1", "#e83e8c", "#dc3545", "#fd7e14", 
                "#ffc107", "#28a745", "#20c997", "#17a2b8", "#6c757d", "#343a40", 
                "#007bff", "#6c757d", "#28a745", "#17a2b8", "#ffc107", "#dc3545"];
    
    $course_ids = array_column($data->allocations, "course_id");
    $course_ids = array_unique($course_ids);

    // set
    $color_set = [];

    // color coding
    foreach($course_ids as $key => $each) {
        $color_set[$each] = $colors[$key];
    }

    // loop through each day
    for ($d = 0; $d < $data->days; $d++) {
        $row = "<tr>";

        // set the day name of the week
        $row .= "<td {$d_style}>".($days[$d] ?? null)."</td>";

        // loop through the slots
        for ($i = 0; $i < $slots; $i++) {
            // set the key
            $info = "";
            $bg_color = "";
            $key = ($d + 1)."_".($i + 1);

            // get the data
            $cleaned = isset($data->allocations[$key]) ? $data->allocations[$key] : null;

            // set the information to display
            if(!empty($cleaned)) {
                $bg_color = "style='background:{$color_set[$cleaned->course_id]};color:#fff'";
                $info = !$codeOnly ? $cleaned->course_name. " (" : null; 
                $info .= "<strong>{$cleaned->course_code}</strong>";
                $info .= !$codeOnly ? " )" : null; 
            }
            // append the information
            $row .= "<td {$bg_color} align='center' id='{$key}'>{$info}</td>";
        }
        $row .= "</tr>";
        $html_table .= $row;
    }

    $html_table .= "</tr>";
    $html_table .= "</table>";

    // print 
    print_r($html_table);
    exit;
}

// if nothing was set
print "Access Denied!";