<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $isStudent, $isParent, $defaultClientData, $isWardParent, $isWardTutorParent, $isTeacher, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Grade Book";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!in_array("class_assessment", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["class_assessment"]);
    echo json_encode($response);
    exit;
}

// item id
$course = null;
$_today = date("l");
$today_lessons_list = "";
$course_id = $SITEURL[1] ?? null;
$class_id = $_GET["class_id"] ?? null;
$timetable_id = $_GET["timetable_id"] ?? null;
$single_student_id = $_GET["student_id"] ?? null;

// is today part of the official school days
$schoolDay = in_array($_today, $clientPrefs->opening_days);

// accepted url
$accepted = ["attendance", "comments", "grading", "assessment"];

// url
$url = $SITEURL[2] ?? "attendance";
$url = in_array($url, $accepted) ? $url : "attendance";
$title = ucwords($url);

// if the course id is not empty
if(!empty($course_id)) {
    
    // bypass the request
    $item_param = (object) [
        "clientId" => $clientId,
        "course_id" => $course_id,
        "userData" => $defaultUser,
        "client_data" => $defaultUser->client,
        "full_attachments" => true,
        "full_details" => true,
        "limit" => 1
    ];
    
    // get the course information
    $data = load_class("subjects", "controllers", $item_param)->list($item_param);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
        echo json_encode($response);
        exit;
    }

    // set more parameters
    $course = $data["data"][0];
}

// init values
$section_content = "<div class='card'><div class='card-body p-2'>";

// get the attendance record
$param = (object) [
    "limit" => 1,
    "clientId" => $clientId, 
    "course_id" => $course_id,
    "class_id" => $class_id,
    "timetable_id" => $timetable_id,
    "client_data" => $defaultUser->client
];

// initial parameters
$count = 0;
$isFound = false;
$lessonsList = [];
$_class_id = null;
$_student_id = null;
$_courses_ids = null;
$today = date("d-m-Y");
$timetableObj = load_class("timetable", "controllers", $param);

// set the parameters to use
$_param = (object) [
    "clientId" => $clientId,
    "class_id" => $_class_id,
    "academic_year" => $defaultAcademics->academic_year,
    "academic_term" => $defaultAcademics->academic_term,
    "order_by" => "b.id",
    "minified" => true
];

// set the courses ids
if($isParent) {
    // set the courses id for the student or parent logged in
    $_student_id = $session->student_id;
    $_class_id = $session->student_class_id;
    $_param->class_id = $session->student_class_id;
    $_courses_ids = $session->student_courses_id;
} elseif($isStudent) {
    // empty the courses ids
    $_param->class_id = $session->student_class_id;
    $_class_id = $session->student_class_id;
    $_student_id = $defaultUser->user_id;
    $_courses_ids = null;
} elseif($isTeacher) {
    // set the courses id using the courses for which this user has permission to view
    $_param->course_tutor = $defaultUser->user_id;
    $_courses_ids = $defaultUser->user_id;
}

// set the filter to use
$_filter = $schoolDay ? "today" : "unspecified";

// load the days lesson notes list
$todayTimetableLessons = $timetableObj->teacher_timetable($_courses_ids, $clientId, $_filter, false, $_class_id);

// confirm that there is a lesson for today
$lessonsList = load_class("subjects", "controllers")->list($_param)["data"] ?? [];

// check if the lesson is empty
if(!empty($lessonsList) && is_array($lessonsList)) {
    // set new lessons list
    $n_lessons_list = [];

    // loop through the lessons list
    foreach($lessonsList as $_lesson) {
        $f_key = $_lesson->class_name . " Subjects";
        $n_lessons_list[$f_key][$_lesson->item_id] = $_lesson;
    }

    // add select option
    if(empty($class_id)) {
        $today_lessons_list .= "<option>Select Lesson to View</option>";
    }

    // clean date
    $__date = date("w");

    // loop through the array list
    foreach($n_lessons_list as $key => $each_class) {

        $clean_key = explode("__", $key)[0];
        $today_lessons_list .= "<optgroup label='".strtoupper($key)."'>";
        foreach($each_class as $lesson) {

            // if the class id isset and the course id is equal
            if(($lesson->class_id == $class_id) && ($lesson->item_id == $course_id)) {
                $isFound = true;
            }
            $date_title = null;
            
            // disable the current course displayed
            $today_lessons_list .= "<option ".(!empty($class_id) && (($lesson->item_id == $course_id) && $lesson->class_id == $class_id) ? "selected" : null)." value='{$lesson->item_id}/{$url}?class_id={$lesson->class_id}'>{$lesson->class_name} | {$lesson->name}</option>";
        }
        $today_lessons_list .= "</optgroup>";
    }
    
}

// if is found
if(!$isFound || empty($lessonsList)) {
    $timetable_id = null;
}

// is attendance panel
$isAttendance = (bool) ($url === "attendance");
$isGrading = (bool) ($url === "grading");

// append the lesson script
$response->scripts[] = ["assets/js/lessons.js"];
$hasPermission = (bool) $isTeacher || $isAdmin;
    
// set the actual information to show
if(in_array($url, ["attendance", "grading"])) {

    // init value
    $showLogAttendance_Button = false;
    $formatTimetable = format_timetable($todayTimetableLessons);

    // set the timetable id
    $timetable_id = isset($formatTimetable[$course_id]) ? $formatTimetable[$course_id]["timetable_id"] : $timetable_id;

    // run this section if the class id was parsed
    if(!empty($class_id)) {

        // set the init value
        $student_performance = null;
        $single_student_name = null;

        // if not attendance then set the timetable_id to null
        if(!$isAttendance) {
            $param->timetable_id = null;
        }

        // get the course attendance list
        $courseAttendance = $timetableObj->lesson_record_data($param);

        // get the data
        $courseAttendance = !empty($courseAttendance["data"]) ? $courseAttendance["data"][0] : [];

        // data to query
        $_query_data = $isAttendance ? "students_attendance_data" : "students_grading_data";
        $_function = $isAttendance ? "format_daily_attendance" : "format_student_grade";

        // get the student data
        $s_data = !empty($courseAttendance) ? $courseAttendance->{$_query_data} : [];

        // get the students list
        $students_list = $myClass->pushQuery(
            "TRIM(a.name) AS name, a.unique_id, a.image, a.id", 
            "users a LEFT JOIN classes b ON b.id = a.class_id",
            "b.item_id = '{$class_id}' AND a.client_id='{$clientId}' 
            AND a.user_type='student' AND a.status='1' AND 
            a.user_status IN ({$myClass->default_allowed_status_users_list})
            ".(!empty($_student_id) ? " AND (a.item_id IN {$myClass->inList($_student_id)})" : null)."
            ORDER BY name
            LIMIT ".(!empty($_student_id) ? 1 : $myClass->global_limit)."");
        
        // IF YOU WANT TO INCLUDE GROUP WORK TO THE ASSESSMENT LIST DISPLAY
        // JUST CHANGE THE VALUE $myClass->append_groupwork_to_assessment TO "true" IN THE models.php FILE;

        // set the content
        $section_content .= "
        <div class=\"table-responsive\">
        <table cellpadding='5px' class=\"table table-bordered table-striped\">
            <thead>
                <tr class=\"text-center\">
                    <th width=\"5%\">#</th>
                    <th width=\"20%\">Student</th>";


                if($isAttendance){
                    $section_content .= "
                        <th>{$title}</th>
                        <th class=\"bg-warning text-white font-13 font-weight-bold\" width=\"5%\">Late</th>
                        <th class=\"bg-danger text-white font-13 font-weight-bold\" width=\"5%\">Absent</th>
                        <th class=\"bg-success text-white font-13 font-weight-bold\" width=\"5%\">Late (Excused)</th>
                        <th class=\"bg-danger text-white font-13 font-weight-bold\" width=\"5%\">Absent (Excused)</th>"; 
                } else {
                    // get the width
                    $width = round((75 / count($myClass->assessment_group)), 2); 
                    // loop through the sba columns
                    foreach($myClass->assessment_group as $sba_item) {
                        $section_content .= "<th class=\"bg-{$myClass->assessment_color_group[$sba_item]} text-white font-13 font-weight-bold\" width=\"{$width}%\">{$sba_item}</th>";
                    }
                }
                $section_content .= "</tr>
            </thead>
            <tbody>";

        // new variable
        $previewStudent = true;
        $students_array_list = [];
        $student_performance_array = [];
        $students_attendance_grading_list = [];
        $additional_checker = (bool) ($schoolDay && !empty($timetable_id));
        
        // if the record is an array
        if(is_array($students_list)) {

            // list the students here
            foreach($students_list as $student) {

                $student->name = random_names($student->name);
                
                $count++;
                // variable
                $button = null;
                $myid = $student->id;
                $students_array_list[$myid] = $student;
                $student_name = str_ireplace("'", "", $student->name);

                // get the student attendance lost
                $mylog = $isAttendance ? ($s_data[$myid]["dates"] ?? []) : $s_data;
                
                // set the content 
                $section_content .= "<tr data-row_search='name' data-student_fullname='{$student->name}' data-student_unique_id='{$student->unique_id}' data-row_id='{$myid}'>";
                $section_content .= "<td>{$count}</td>";
                $section_content .= "
                <td class='user_name nounderline font-16'>
                    <span ".($isGrading && $hasPermission ? 'title="Click to view grades of '.$student_name.'" onclick="load(\'gradebook/'.$course_id.'/grading?class_id='.$class_id.'&timetable_id='.$timetable_id.'&student_id='.$myid.'\');"' : null).">{$student->name}</span>
                </td>";

                // show this section is the attendance item is parsed
                if($isAttendance) {

                    // if the current day's attendance has not been logged 
                    if(((!isset($mylog[$today]) && $additional_checker)) && $hasPermission) {
                        // set a value for the $showLogAttendance_Button
                        $showLogAttendance_Button = true;
                        // set the new button
                        $button = "<button onclick='return show_Attendance_Grading_Log_Form(\"{$myid}\",\"{$student_name}\");' class='btn btn-secondary font-bold font-14 bg-black pt-1 pb-1' title='Record Attendance'>New</button>";
                    }
                    // push the student record into the array
                    $students_attendance_grading_list[$myid] = $mylog;

                    // format the list result
                    $mylist = $_function($mylog, $myid, $student_name);

                    // priint the attendance list and other summary data
                    $section_content .= "<td class='attendance_content'>{$mylist} {$button}</td>";
                    $section_content .= "<td class='attendance_count' a_state='late'>".($s_data[$myid]["summary"]["late"] ?? null)."</td>";
                    $section_content .= "<td class='attendance_count' a_state='absent'>".($s_data[$myid]["summary"]["absent"] ?? null)."</td>";
                    $section_content .= "<td class='attendance_count' a_state='late_excused'>".($s_data[$myid]["summary"]["late_excused"] ?? null)."</td>";
                    $section_content .= "<td class='attendance_count' a_state='absent_excused'>".($s_data[$myid]["summary"]["absent_excused"] ?? null)."</td>";
                } else {

                    // loop through the sba columns
                    foreach($myClass->assessment_group as $sba_item) {

                        // convert the sba item to lowercase
                        $sba_item = strtolower($sba_item);
                        $the_marks = $mylog[$sba_item]["students"][$myid] ?? [];
                        $marks_obtained = $_function($the_marks, $myid, $student_name, $sba_item);

                        // push the student record into the array
                        $students_attendance_grading_list[$myid][$sba_item] = $mylog[$sba_item]["students"][$myid] ?? [];

                        // print the grades awarded to the student
                        $section_content .= "<td class='student_grading' a_state='{$sba_item}'>{$marks_obtained["the_list"]}";

                        // either the $single_student_id is empty or the student id is equal to the current student
                        if(empty($single_student_id) || ($single_student_id === $myid)) {

                            // set the student name
                            $single_student_name = strtoupper($student_name);

                            // count the number of marks obtained
                            $count_marks_obtained = count($marks_obtained["the_marks"]);
                            $total_marks = array_sum($marks_obtained["the_marks"]);
                            $actualmarks = array_sum($marks_obtained["actualmarks"]);
                            
                            // add to the array
                            $student_performance_array[$sba_item] = [
                                "count" => isset($student_performance_array[$sba_item]["count"]) ? ($student_performance_array[$sba_item]["count"] + $count_marks_obtained) : $count_marks_obtained,
                                "marks" => isset($student_performance_array[$sba_item]["marks"]) ? ($student_performance_array[$sba_item]["marks"] + $total_marks) : $total_marks,
                                "actualmarks" => isset($student_performance_array[$sba_item]["actualmarks"]) ? ($student_performance_array[$sba_item]["actualmarks"] + $actualmarks) : $actualmarks
                            ];
                        }

                        // set the button to show
                        // $section_content .= !$hasPermission ? grading_button($myid, $student_name, $sba_item) : null;
                        
                        $section_content .= "</td>";
                    }
                }
                $section_content .= "</tr>";

            }

        }

        $section_content .= "</tbody></table>
        </div>";

        // set the student performance
        $performance_chart = [];

        // loop through the students performance array list
        foreach($student_performance_array as $key => $value) {

            // set some parameters
            $average_score = $value["count"] > 0 ? round(($value["marks"] / $value["count"]), 2) : "N/A";
            $overall_performance = $value["count"] > 0 ? round((($value["marks"] / $value["actualmarks"]) * 100), 2) : 0;
            $actualcount = $isWardParent || $single_student_id ? $value["count"] : ($value["count"] / count($students_list));

            $color = $overall_performance > 75 ? "text-success" : (
                $overall_performance > 50 ? "text-warning" : "text-danger"
            );

            $student_performance .= "
            <tr>
                <td>".strtoupper($key)."</td>
                <td class='text-center'>{$actualcount}</td>
                <td class='text-center'>{$value["marks"]}</td>
                <td class='text-center'>{$average_score}</td>
                <td class='text-center'>".($value["actualmarks"] > 0 ? $value["actualmarks"] : "N/A")."</td>
                <td class='text-center {$color}'>".($overall_performance > 0 ? "{$overall_performance}%" : "N/A")."</td>
            </tr>";
            $performance_chart[$key] = $overall_performance;
        }

        // push the result in an array
        $response->array_stream["students_array_list"] = $students_array_list;
        $response->array_stream["students_attendance_grading_list"] = $students_attendance_grading_list;
        $response->array_stream["performance_chart"]["overall"] = $performance_chart;
        $response->array_stream["performance_chart"]["summation"] = round(array_sum($performance_chart), 2);

        // set the student performance chart
        $student_performance_chart = "
        <div class='col-lg-12 p-0'>
            <div class='card'>
                <div class='card-header pb-0'>
                    <div class='d-flex justify-content-between width-per-100'>
                        <div class='mb-2'>
                            STUDENT PERFORMANCE CHART ".
                            (!empty($single_student_id) ? 
                                "OF - <span class='text-primary'>{$single_student_name}</span>" : null
                            )."
                        </div>
                        ".(!empty($single_student_id) ?
                            "<div class='mb-2'>
                                <button onclick=\"return load('gradebook/{$course_id}/grading?class_id={$class_id}&timetable_id={$timetable_id}');\" class='btn btn-sm btn-outline-primary'>
                                    <i class='fa fa-arrow-circle-left'></i> Go Back
                                </button>
                            </div>" : null
                        )."
                    </div>
                </div>
                <div class='card-body' id='performance_chart'>
                    <div class='row'>
                        <div class='col-md-7 table-responsive mb-3'>
                            <table class='table table-bordered'>
                                <thead>
                                    <tr class='bg-primary'>
                                        <th class='text-white'>DESCRIPTION</th>
                                        <th class='text-white' style='text-align:center'>COUNT</th>
                                        <th class='text-white' style='text-align:center'>TOTAL SCORE</th>
                                        <th class='text-white' style='text-align:center'>AVERAGE SCORE</th>
                                        <th class='text-white' style='text-align:center'>EXPECTED SCORE</th>
                                        <th class='text-white' style='text-align:center'>PERFORMANCE RATE</th>
                                    </tr>
                                </thead>
                                <tbody>{$student_performance}</tbody>
                            </table>
                        </div>
                        <div class='col-md-5 mb-3'>
                            <canvas style='max-height:355px;height:355px;' id='student_performance'></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
    }

}

// if the url is assessment
elseif($url === "assessment") {
    
    // create new object
    $hasUpdate = $accessObject->hasAccess("update", "assignments");
    $assessmentObj = load_class("assignments", "controllers");

    // the query parameter to load the user information
    $assignments_param = (object) [
        "show_marks" => true,
        "clientId" => $clientId,
        "userData" => $defaultUser,
        "client_data" => $defaultClientData,
        "class_id" => $class_id,
        "course_id" => $course_id,
        "limit" => 500
    ];
    $item_list = $assessmentObj->list($assignments_param);

    $assignments = "";
    $formated_content = $assessmentObj->format_list($item_list, true);

    // new items list
    $assessment_array = $formated_content["array_list"];
    $assignments = $formated_content["assignments_list"];

    // assessment array
    $response->array_stream["assessment_array"] = $assessment_array;

    // set the class id
    // run this section if the class id was parsed
    $section_content .= "
    <div id='assessment_container'>
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-striped raw_datatable\">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Due Date</th>
                        ".($hasUpdate ? '
                            <th width=\"10%\">Assigned</th>
                            <th>Handed In</th>
                            <th>Marked</th>' : '<th>Awarded Mark</th>'
                        )."
                        <th>Date Created</th>
                        <th>Status</th>
                        <th align=\"center\" width=\"12%\"></th>
                    </tr>
                </thead>
                <tbody>{$assignments}</tbody>
            </table>
        </div>";
    $section_content .= '
    <input type="hidden" hidden disabled name="class_id" value="'.$class_id.'">
    <input type="hidden" hidden disabled name="course_id" value="'.$course_id.'">
    <input type="hidden" hidden disabled name="timetable_id" value="'.($_GET["timetable_id"] ?? null).'">
    </div>';
}

$section_content .= "</div></div>";

// set the response data
$response->html = '
<section class="section">
    <div class="section-header">
        <h1>'.$pageTitle.': <span class="text-primary">'.(!empty($course) ? "{$course->name} ({$course->course_code})" : null).'</span></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'courses">Subjects List</a></div>
            <div class="breadcrumb-item">'.$pageTitle.'</div>
        </div>
    </div>';
    // if the term has ended
    if(($isAdminAccountant || $isTutorAdmin)) {
        $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
    }
    $response->html .= '
    <div class="row" id="course_lesson">
        '.(!empty($course) ?
            '<div class="col-md-8 mb-3 table-responsive width-100-per">
                <div class="btn-group mb-0" role="group">
                    <button type="button" onclick="load(\'gradebook/'.$course_id.'/attendance?class_id='.$class_id.'&timetable_id='.$timetable_id.'\');" class="btn btn-outline-dark font-15 '.($url == "attendance" ? "active" : null).'"><i class="fa sm-hide fa-user-check"></i> Attendance</button>
                    <!--<button type="button" onclick="load(\'gradebook/'.$course_id.'/comments?class_id='.$class_id.'&timetable_id='.$timetable_id.'\');" class="btn btn-outline-dark font-15 '.($url == "comments" ? "active" : null).'"><i class="fa sm-hide fa-comments"></i> Comment</button>-->
                    <button type="button" onclick="load(\'gradebook/'.$course_id.'/grading?class_id='.$class_id.'&timetable_id='.$timetable_id.'\');" class="btn btn-outline-dark font-15 '.($url == "grading" ? "active" : null).'"><i class="fa sm-hide fa-chart-line"></i> Grading</button>
                    <button type="button" onclick="load(\'gradebook/'.$course_id.'/assessment?class_id='.$class_id.'&timetable_id='.$timetable_id.'\');" class="btn btn-outline-dark font-15 '.($url == "assessment" ? "active" : null).'"><i class="fa sm-hide fa-tags"></i> Assessments</button>
                </div>
            </div>
        ' : '<div class="col-md-8 mb-3"></div>').'
        <div class="col-md-4 mb-3">
            <div class="form-group mb-0">
                <select data-width="100%" class="selectpicker form-control" name="lesson_subject_id">
                    '.$today_lessons_list.'
                </select>
            </div>
        </div>
        <div class="col-lg-12 mt-0">
            '.(
                !$schoolDay && $isAttendance ?
                "".(!empty($course_id) && $isTeacher ? 
                        "<div class='alert font-bold alert-warning p-2 text-center'>ATTENDANCE CANNOT BE MARKED TODAY.</div>" : 
                        (
                            empty($course_id) && !$isTeacher ? 
                                "<div class='alert font-bold alert-warning p-2 text-center'>SORRY! YOU HAVE NOT SELECTED ANY SUBJECT YET</div>" : 
                                "<div class='alert font-bold alert-warning p-2 text-center'>SORRY! YOU HAVE NOT SELECTED ANY SUBJECT YET</div>"
                        )
                    )."
                </div>" : null
            ).'
            '.(
                empty($course_id) ? 
                    "<div hidden class='alert font-bold alert-warning p-2 text-center'>SORRY! YOU HAVE NOT SELECTED ANY SUBJECT YET</div>" 
                : null
            ).'
            '.(($url === "assessment") && ($isTeacher || $isAdmin) ? 
                "<div class='alert font-bold alert-warning p-2 text-center'>NB: YOU CAN ONLY EXPORT AWARDED MARKS TO THE GRADING SECTION ONLY WHEN THE TEST HAS BEEN DULY MARKED AS CLOSED.</div>" : null
            ).'
        </div>
    </div>
    <div class="row '.(!$course_id ? "hidden" : null).'">
        <div class="col-md-'.($isAttendance && $showLogAttendance_Button ? 10 : 12).'">
            '.(in_array($url, ["attendance", "grading"]) && $hasPermission ? $myClass->quick_student_search_form : null).'
        </div>
        '.($isAttendance && $showLogAttendance_Button && $course_id ? 
            "<div class='col-md-2'>
                <label class='text-dark'>Mark Register</label>
                <button onclick='return show_Bulk_Attendance()' class='btn-block btn btn-outline-primary'><i class='fa fa-check-circle'></i> Bulk Attendance</button>
            </div>"
            : null
        ).'
    </div>
    '.(($isTeacher || $isAdmin) && $single_student_id && $isGrading && !empty($course_id) ? $student_performance_chart : null).'
    '.(strlen($section_content) < 100 ? 
        no_record_found("No Subject Selected", "You must first select a subject in order to render the gradebook details and its insights.", null, "Event", false, "fa-book-reader") : 
        $section_content
    ).'
    '.(($isStudent || (($isTeacher || $isAdmin) && empty($single_student_id))) && $isGrading && !empty($course_id) ? $student_performance_chart : null).'
</section>';

// popup window
if(in_array($url, ["attendance", "grading"])) {
    
    $score_line = "";
    if(!$isAttendance) {

        // init
        $count = 0;

        // score line table
        $score_line .= "<table width='100%' border='1'>";
        
        // loop through the list
        for($i = 1; $i <= 10; $i++) {
            $count++;
            $score_line .= "<tr>";

            // loop through the count
            for($ii = 1; $ii <= 10; $ii++) {
                $score_line .= "<td data-grade_value='{$count}' class='grade_select'>{$count}</td>";
                $count++;
            }
            $count -= 1;
            $score_line .= "</tr>";
        }
        $score_line .= "</table>";
    }

    // show form
    $response->html .= '
    <div data-backdrop="static" data-keyboard="false" class="modal fade" id="log_grading_attendance">
        <div class="modal-dialog '.(!$isAttendance ? "modal-lg" : null).'" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span data="title">Attendance Log</span><span data="student_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        '.($isAttendance ?
                            '<div class="col-md-12">
                                <div class="form-group" id="attedance_selector">
                                    <span style="border:solid 1px #fff" title="Click to Select" data-option="present" class="badge cursor mb-2 hover-border badge-success">Present</span>
                                    <span style="border:solid 1px #fff" title="Click to Select" data-option="late" class="badge cursor mb-2 hover-border bg-warning">Late</span>
                                    <span style="border:solid 1px #fff" title="Click to Select" data-option="absent" class="badge cursor mb-2 hover-border badge-danger">Absent</span>
                                    <span style="border:solid 1px #fff" title="Click to Select" data-option="late_excused" class="badge cursor mb-2 hover-border badge-warning">Late (Excused)</span>
                                    <span style="border:solid 1px #fff" title="Click to Select" data-option="absent_excused" class="badge cursor mb-2 hover-border badge-danger">Absent (Excused)</span>
                                </div>
                            </div>' : 
                            '<div data-container="review" class="hidden col-md-6">
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="text" readonly="readonly" name="_date" class="form-control">
                                </div>
                            </div>
                            <div data-container="review" class="hidden col-md-6">
                                <div class="form-group">
                                    <label>Grade:</label>
                                    <input type="text" readonly="readonly" name="_grade" class="form-control">
                                </div>
                            </div>
                            <div data-container="new" class="col-md-6 p-1">'.$score_line.'</div>'
                        ).'
                        <div class="col">
                            <div class="form-group">
                                <label for="comments">Comment for '.($isAttendance ? "Attendance" : "Grade").' (optional):</label>
                                <textarea type="text" maxlength="255" name="comments" id="comments" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-0">
                        <input type="hidden" disabled name="allow_selection" value="0">
                        <input type="hidden" hidden disabled name="student_id">
                        <input type="hidden" hidden disabled name="student_name">
                        <input type="hidden" hidden disabled name="grading_type">
                        <input type="hidden" hidden disabled name="grading_date">
                        <input type="hidden" hidden disabled name="class_id" value="'.$class_id.'">
                        <input type="hidden" hidden disabled name="course_id" value="'.$course_id.'">
                        <input type="hidden" hidden disabled name="timetable_id" value="'.$timetable_id.'">
                        <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                        '.($schoolDay && $isAttendance ?
                            '<button onclick="return log_Student_'.$title.'();" class="btn submit_button btn-success">Log Attendance</button>' : 
                            '<button onclick="return log_Student_'.$title.'();" class="btn submit_button btn-success">Grade Student</button>'
                        ).'
                    </div>
                </div>
            </div>
        </div>
    </div>';

    // load if the url is attendance
    if($isAttendance && $showLogAttendance_Button) {
        // show form
        $response->html .= '
        <div data-backdrop="static" data-keyboard="false" class="modal fade" id="bulk_Attendance_Log">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span data="title">Bulk Lesson Attendance Log - '.date("l, jS F, Y").'</span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pb-2 border-bottom"></div>
                    <div class="modal-footer pb-2">
                        <input type="hidden" hidden disabled name="class_id" value="'.$class_id.'">
                        <input type="hidden" hidden disabled name="course_id" value="'.$course_id.'">
                        <input type="hidden" hidden disabled name="timetable_id" value="'.$timetable_id.'">
                        <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                        <button onclick="return log_Bulk_Attendance();" class="btn submit_button btn-success">Log Bulk Attendance</button>
                    </div>
                </div>
            </div>
        </div>';
    }
    
}

// print out the response
echo json_encode($response);
?>