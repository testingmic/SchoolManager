<?php
/**
 * Render attendance day chart
 * 
 * @return string
 */
function render_attendance_table($dataset = []) {
    // Get unique dates across all classes
    $allDates = [];
    foreach (($dataset['summary'] ?? $dataset) as $class => $records) {
        foreach ($records as $date => $stats) {
            $allDates[$date] = true;
        }
    }
    $allDates = array_keys($allDates);
    sort($allDates);

    $html_content = "<thead>
    <tr><th width='15%'>Class</th>";
    foreach ($allDates as $date) {
        $day = date('jS M', strtotime($date));
        $html_content .= "<th>{$day}</th>";
    }
    $html_content .= "</tr></thead>";
    $html_content .= "<tbody class='class_summary_attendance_rate'>";

    // Fill rows
    foreach (($dataset['summary'] ?? $dataset) as $className => $records) {
        $html_content .= "<tr><td width='15%'>{$className}</td>";
        foreach ($allDates as $date) {
            if (isset($records[$date])) {
                $p = $records[$date]['Present'];
                $a = $records[$date]['Absent'];
                $s = $records[$date]['Class Size'];
                $html_content .= "
                <td>
                    <div class='mb-1'>👥 {$s}</div>
                    <div class='mb-1'><span class='text-success'>✔</span> {$p}</div> 
                    <div class='mb-1'>❌ {$a}</div>
                </td>";
            } else {
                $html_content .= "<td>-</td>";
            }
        }
        $html_content .= "</tr>";
    }
    $html_content .= "</tbody>";

    return $html_content;

}

/**
 * Render the class attendance
 * 
 * @param array $attendance
 * @param string $class_id
 * @param string $baseUrl
 * 
 * @return string
 */
function render_class_attendance($attendance = [], $class_id = null, $baseUrl = null) {
    $html_content = "";

    $i = 0;
    foreach(($attendance["attendanceRate"] ?? 0) as $className => $each) {
        $i++;
        if(empty($each['totalDays'])) continue;
        $html_content .= "
        <tr>
            <td class='3%'>{$i}</td>
            <td>{$className}</td>
            <td class='text-center'>{$each['Size']}</td>
            <td class='text-center'>{$each['totalDays']}</td>
            <td class='text-center text-success'>{$each['Present']}</td>
            <td class='text-center text-danger'>{$each['Absent']}</td>
            <td class='text-center text-success'>{$each['presentRate']}%</td>
            <td class='text-center text-warning'>{$each['absentRate']}%</td>
            ".(empty($class_id) ? "
            <td class='text-center'>
                <button onclick='return loadPage(\"{$baseUrl}attendance/summary/{$each['Id']}\")' class='btn btn-sm p-1 pr-2 pl-2 btn-outline-success'><i class='fas fa-chart-bar'></i> View</button>
            </td>" : null)."
        </tr>";
    }
    return $html_content;
}

/**
 * Render the summary card
 * 
 * @param string $label
 * @param string $icon
 * @param string $color
 * 
 * @return string
 */
function render_summary_card($value = 0, $label = null, $icon = null, $color = null, $column = "col-lg-3 col-md-4", $icon_class = null) {
    return '<div class="'.$column.' transition-all duration-300 transform hover:-translate-y-1">
            <div class="card">
                <div class="card-body pb-2 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-black mb-0">'.$label.'</h6>
                            <span data-count="'.strtolower(str_ireplace(" ", "_", $label)).'" class="font-weight-bold font-25 mb-0">'.$value.'</span>
                        </div>
                        <div class="col-auto '.$icon_class.'">
                            <div class="card-circle l-bg-'.$color.' text-white">
                                <i class="fas '.$icon.'"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
}

/**
 * Render the class student attendance
 * 
 * @param array $attendance
 * @param string $class_id
 * @param string $baseUrl
 * 
 * @return string
 */
function class_student_attendance($attendance = []) {
    $html_content = "";

    $breakdown = $attendance['breakdown'] ?? [];
    $i = 0;
    foreach(($attendance['summary'] ?? []) as $id => $student) {
        $i++;

        $presentRate = $student['present'] > 0 ? round(($student['present'] / $student['expected']) * 100, 2) : 0;
        $absentRate = $student['absent'] > 0 ? round(($student['absent'] / $student['expected']) * 100, 2) : 0;

        $html_content .= "
        <tr>
            <td class='3%'>{$i}</td>
            <td>{$breakdown[$id]['name']}</td>
            <td class='text-center'>{$student['expected']}</td>
            <td class='text-center'>{$student['present']}</td>
            <td class='text-center'>{$student['absent']}</td>
            <td class='text-center'>{$presentRate}%</td>
            <td class='text-center'>{$absentRate}%</td>
        </tr>";
    }
    return $html_content;
}

/**
 * Setup the admin attendance summary cards
 * 
 * @param string $col
 * @param boolean $append
 * 
 * @return string
 */
function admin_summary_cards($col = "col-lg-3 col-md-3", $append = false) {
    $html = '
    <div class="'.$col.' col-sm-6">
        <div class="card card-statistic-1">
            <i class="fas small-hidden fa-user-graduate card-icon col-green"></i>
            <div class="card-wrap">
                <div class="padding-15">
                    <div class="text-right">
                        <h3 data-attendance_count="student.Marked_Days" class="font-light mb-0">0</h3>
                        <span class="text-black font-13">Student Marked Days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="'.$col.' col-sm-6">
        <div class="card card-statistic-1">
            <i class="fas small-hidden fa-user-tie card-icon col-orange"></i>
            <div class="card-wrap">
                <div class="padding-15">
                    <div class="text-right">
                        <h3 data-attendance_count="staff.Marked_Days" class="font-light mb-0">0</h3>
                        <span class="text-black font-13">Staff marked Days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="'.$col.' col-sm-6">
        <div class="card card-statistic-1">
            <i class="fas small-hidden fas fa-user-check card-icon col-blue card-icon"></i>
            <div class="card-wrap">
                <div class="padding-15">
                    <div class="text-right">
                        <h3 data-attendance_count="attendanceRate" class="font-light mb-0">0</h3>
                        <span class="text-black">Attendance Rate</span>
                    </div>
                </div>
            </div>
        </div>
    </div>';

    if(!empty($append)) {
        $html .= '
            <div class="'.$col.' col-sm-6">
                <div class="card card-statistic-1">
                    <i class="fas small-hidden fa-ticket-alt card-icon col-green"></i>
                    <div class="card-wrap">
                        <div class="padding-15">
                            <div class="text-right">
                                <h3 data-attendance_count="ActiveSchoolDays" class="font-light mb-0">0</h3>
                                <span class="text-black font-13">School Active Days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    }
    return $html;
}

/**
 * Render the qr code inactive
 * 
 * @return string
 */
function render_qr_code_inactive($baseUrl = null) {
    return "<div class='max-w-4xl mx-auto px-4 pt-4'>
        <div id='no_record_found_container' class='backdrop-blur-xl mt-2 backdrop-saturate-150 rounded-2xl border border-solid-gray dark:bg-opacity-20 transition-all duration-300 p-6 bg-white dark:bg-gray-900/50 border-white/10 dark:border-gray-700/50'>
            <div class='dark:text-gray-300'>
                <div class='text-center py-6'>
                    <div
                        class='w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4'>
                        <i class='fa fa-school text-3xl text-gray-500 dark:text-gray-400'></i>
                    </div>
                    <h4 class='text-lg font-25 font-medium text-gray-900 dark:text-white mb-2 text-red-500'>School Inactive</h4>
                    <p class='text-gray-600 dark:text-gray-400 mb-6'>The school is currently inactive. Please contact the school administrator for more information.</p>
                    
                    <div class='mt-3'>
                        <button class='w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-2 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2' onclick='return  returnToHome()'><i class='fa fa-arrow-left'></i> Go Back</button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>";
}

/**
 * Render the qr code header
 * 
 * @return string
 */
function render_qr_code_header($baseUrl = null, $clientId = null) {
    return '<div class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><a href="'.$baseUrl.'qr_code?client='.$clientId.'">QR Scanner</a></h1>
                        <p class="text-gray-600">Scan QR Code to Log Attendance</p>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

/**
 * Get the card found message
 * 
 * @return string
 */
function card_found_message($schoolName = null) {
    return "This card is the property of {$schoolName}. If found, please return to the school address below or contact us immediately.";
}

/**
 * Render the card preview
 * 
 * @param object $cardSettings
 * @param object $defaultClientData
 * 
 * @return string
 */
function render_card_preview($cardSettings = null, $defaultClientData = null, $useData = false) {
    
    $start = $cardSettings->issue_date ?? "2020-01-01";
    $end = $cardSettings->expiry_date ?? "2025-01-01";

    $type = "Student";
    if($useData) {
        $type = $cardSettings->user_type == "student" ? "Student" : "Employee";
    }

    $html = '
    <div class="card-preview" style="min-width: 300px;">
        <div class="card-preview-body">
            <div class="card-preview-front">
                <div class="card-preview-front-header pb-0" style="width: 100%; padding-right: 10px; padding-left: 10px; padding-top: 5px;">
                    <div style="float: left; width: 15%;">
                        <img width="50" height="40" src="'.($defaultClientData->baseUrl ?? "").''.$defaultClientData->client_logo.'" alt="'.$defaultClientData->client_name.'">
                    </div>
                    <div class="text-center" style="float: left; width: 85%;">
                        <div class="mb-0" style="font-size: 22px; margin-bottom: 0px; font-weight: bold;">'.$defaultClientData->client_name.'</div>
                        <p class="mb-0" data-item="card_type" style="font-size: 15px; font-weight: bold;">'.$type.' Identification Card</p>
                    </div>
                </div>
                <div class="card-preview-front-body" style="width:100%; float: left; background-color: '.($cardSettings->front_color ?? "#1E40AF").'; color: '.($cardSettings->front_text_color ?? "#ffffff").';">
                    <div style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 10px;">
                        '.($useData ? $cardSettings->name : "Emmanuel Obeng").'
                        <div style="font-size: 15px; font-weight: bold;">
                            '.($useData ? $cardSettings->unique_id : "M000000001").'
                        </div>
                    </div>
                    <div style="width: 100px; float: left; height: 100px; background-color: #fff; padding: 5px; border-radius: 7px;">
                        <img src="'.($defaultClientData->baseUrl ?? "").'assets/img/avatar.png" style="border-radius: 7px;" width="100%">
                    </div>
                    <div style="margin-left: 30px;float: left; font-size: 13px;" data-item="front_card_details">
                        <div><strong>Gender:</strong></div>
                        <div><strong>Date of Birth:</strong></div>
                        <div><strong>Admission:</strong></div>
                        <div><strong>'.$type.' Type:</strong></div>
                        '.($useData && !empty($cardSettings->house) ? "<div><strong>House:</strong></div>" : "").'
                    </div>
                    <div style="margin-left: 30px;float: left; font-size: 13px;" data-item="front_card_details">
                        <div>'.($useData ? $cardSettings->gender : "Male").'</div>
                        <div>'.($useData ? (empty($cardSettings->date_of_birth) ? "N/A" : $cardSettings->date_of_birth) : "1990-01-01").'<br></div>
                        <div>'.($useData ? (empty($cardSettings->enrollment_date) ? "N/A" : $cardSettings->enrollment_date) : "1990-01-01").'<br></div>
                        <div>'.($useData ? (empty($cardSettings->day_boarder) ? "Regular" : $cardSettings->day_boarder) : "Regular").'<br></div>
                        '.($useData && !empty($cardSettings->house) ? "<div>".$cardSettings->house."</div>" : "").'
                    </div>
                    <div style="width: 105px; text-align: center; font-size: 14px; float: right; background-color: #fff; color: #000; border-radius: 5px; height: 100px;">
                        '.($useData ? '<img src="'.$cardSettings->qr_code.'" style="border-radius: 5px;" width="100%">' : "QR Code Here").'
                    </div>
                </div>
                <div style="text-align: center; font-size: 13px; padding-top: 5px; font-weight: normal;width: 100%; float: left;">
                    Valid: '.date('M Y', strtotime($start)).' - '.date('M Y', strtotime($end)).'
                </div>
            </div>
            
            <div class="card-preview-back" style="background-color: '.($cardSettings->back_color ?? "#DC2626").'; color: '.($cardSettings->back_text_color ?? "#ffffff").';">
                <div style="padding: 10px;">
                    <div style="text-align: center; font-size: 20px; padding: 7px; min-height: 40px; line-height: 1.2; background: rgba(255, 255, 255, 0.1); width: 100%; float: left;">
                        <div>'.($defaultClientData->client_name ?? "").'</div>
                    </div>
                    <div style="text-align: center; width: 100%; padding-top: 25px; padding-bottom: 25px; font-size: 15px;float: left;">
                        <div style="width: 90%; margin: 0 auto;" data-item="back_found_message">
                            '.($cardSettings->back_found_message ?? card_found_message($defaultClientData->client_name)).'
                        </div>
                    </div>
                    <div style="text-align: center; font-size: 13px; border-radius: 10px; padding: 10px; background: rgba(255, 255, 255, 0.1); font-weight: normal;width: 100%; float: left;">
                        <div>If Found, Contact:</div>
                        <div data-item="contact_numbers">'.($defaultClientData->client_contact ?? "").'</div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>';

    return $html;
}

/**
 * Render the language select
 * 
 * @param object $data
 * 
 * @return array
 */
function render_language_select($data = null) {
    
    $languages = [
        "af" => "Afrikaans",
        "am" => "Amharic",
        "ar" => "Arabic",
        "az" => "Azerbaijani",
        "be" => "Belarusian",
        "bg" => "Bulgarian",
        "bn" => "Bengali",
        "bs" => "Bosnian",
        "ca" => "Catalan",
        "ceb" => "Cebuano",
        "cs" => "Czech",
        "cy" => "Welsh",
        "da" => "Danish",
        "de" => "German",
        "el" => "Greek",
        "en" => "English",
        "eo" => "Esperanto",
        "es" => "Spanish",
        "et" => "Estonian",
        "eu" => "Basque",
        "fa" => "Persian",
        "fi" => "Finnish",
        "fil" => "Filipino",
        "fr" => "French",
        "fy" => "Frisian",
        "ga" => "Irish",
        "gd" => "Scottish Gaelic",
        "gl" => "Galician",
        "gu" => "Gujarati",
        "ha" => "Hausa",
        "haw" => "Hawaiian",
        "he" => "Hebrew",
        "hi" => "Hindi",
        "hmn" => "Hmong",
        "hr" => "Croatian",
        "ht" => "Haitian Creole",
        "hu" => "Hungarian",
        "hy" => "Armenian",
        "id" => "Indonesian",
        "ig" => "Igbo",
        "is" => "Icelandic",
        "it" => "Italian",
        "ja" => "Japanese",
        "jv" => "Javanese",
        "ka" => "Georgian",
        "kk" => "Kazakh",
        "km" => "Khmer",
        "kn" => "Kannada",
        "ko" => "Korean",
        "ku" => "Kurdish (Kurmanji)",
        "ky" => "Kyrgyz",
        "la" => "Latin",
        "lb" => "Luxembourgish",
        "lo" => "Lao",
        "lt" => "Lithuanian",
        "lv" => "Latvian",
        "mg" => "Malagasy",
        "mi" => "Maori",
        "mk" => "Macedonian",
        "ml" => "Malayalam",
        "mn" => "Mongolian",
        "mr" => "Marathi",
        "ms" => "Malay",
        "mt" => "Maltese",
        "my" => "Myanmar (Burmese)",
        "ne" => "Nepali",
        "nl" => "Dutch",
        "no" => "Norwegian",
        "ny" => "Chichewa",
        "or" => "Odia (Oriya)",
        "pa" => "Punjabi",
        "pl" => "Polish",
        "ps" => "Pashto",
        "pt" => "Portuguese",
        "ro" => "Romanian",
        "ru" => "Russian",
        "rw" => "Kinyarwanda",
        "sd" => "Sindhi",
        "si" => "Sinhala",
        "sk" => "Slovak",
        "sl" => "Slovenian",
        "sm" => "Samoan",
        "sn" => "Shona",
        "so" => "Somali",
        "sq" => "Albanian",
        "sr" => "Serbian",
        "st" => "Sesotho",
        "su" => "Sundanese",
        "sv" => "Swedish",
        "sw" => "Swahili",
        "ta" => "Tamil",
        "te" => "Telugu",
        "tg" => "Tajik",
        "th" => "Thai",
        "tk" => "Turkmen",
        "tl" => "Tagalog",
        "tr" => "Turkish",
        "tt" => "Tatar",
        "ug" => "Uyghur",
        "uk" => "Ukrainian",
        "ur" => "Urdu",
        "uz" => "Uzbek",
        "vi" => "Vietnamese",
        "xh" => "Xhosa",
        "yi" => "Yiddish",
        "yo" => "Yoruba",
        "zh" => "Chinese",
        "zu" => "Zulu"
    ];

    return $languages;

}
?>