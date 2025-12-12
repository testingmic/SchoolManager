<?php
/**
 * Load the json content
 * 
 * @param array $content
 * 
 * @return string
 */
function preview($content = []) {
    header("Content-Type: application/json");
    echo json_encode($content);exit;
}
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
 * Convert image to base64 data URI for dompdf
 * 
 * @param string $imagePath
 * @param string $baseUrl
 * @return string
 */
function image_to_base64($imagePath, $baseUrl = '') {
    // If already a data URI, return as is
    if (strpos($imagePath, 'data:image') === 0) {
        return $imagePath;
    }

    $initFullPath = $baseUrl . $imagePath;
    
    // If already base64 encoded (without data:image prefix), add prefix
    if (strpos($imagePath, 'base64,') !== false || strpos($initFullPath, 'localhost') !== false) {
        return $initFullPath;
    }
    
    // Handle absolute URLs
    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        $imageData = @file_get_contents($imagePath);
        if ($imageData !== false) {
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo !== false) {
                $mimeType = $imageInfo['mime'];
                return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            }
        }
    }
    
    // Handle relative paths
    $fullPath = $baseUrl . $imagePath;
    
    // Try as absolute URL first
    if (filter_var($fullPath, FILTER_VALIDATE_URL)) {
        $imageData = @file_get_contents($fullPath);
        if ($imageData !== false) {
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo !== false) {
                $mimeType = $imageInfo['mime'];
                return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            }
        }
    }
    
    // Try as local file path (relative to document root)
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $possiblePaths = [
        $imagePath,
        $docRoot . '/' . ltrim($imagePath, '/'),
        dirname($_SERVER['SCRIPT_FILENAME'] ?? '') . '/' . ltrim($imagePath, '/'),
    ];
    
    // Add baseUrl variations if provided
    if (!empty($baseUrl)) {
        // If baseUrl is a URL, try it
        if (filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $possiblePaths[] = rtrim($baseUrl, '/') . '/' . ltrim($imagePath, '/');
        } else {
            // If baseUrl is a path
            $possiblePaths[] = rtrim($baseUrl, '/') . '/' . ltrim($imagePath, '/');
            $possiblePaths[] = $docRoot . '/' . ltrim($baseUrl, '/') . '/' . ltrim($imagePath, '/');
        }
    }
    
    foreach ($possiblePaths as $path) {
        // Skip if path contains http/https (already handled above)
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            continue;
        }
        
        if (file_exists($path) && is_readable($path)) {
            $imageData = @file_get_contents($path);
            if ($imageData !== false) {
                $imageInfo = @getimagesizefromstring($imageData);
                if ($imageInfo !== false) {
                    $mimeType = $imageInfo['mime'];
                    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                }
            }
        }
    }
    
    // Fallback: return original path (dompdf might handle it)
    return $imagePath;
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

    $userImage = !empty($cardSettings->image) ? $cardSettings->image : "assets/img/avatar.png";
    $baseUrl = $defaultClientData->baseUrl ?? "";
    
    // Get absolute path for file existence check
    $basePath = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if (!empty($basePath) && !file_exists($userImage)) {
        // Try with base path
        $fullImagePath = $basePath . '/' . ltrim($userImage, '/');
        if (!file_exists($fullImagePath)) {
            $userImage = "assets/img/avatar.png";
        }
    }

    // Convert images to base64 for dompdf
    $logoBase64 = image_to_base64($defaultClientData->client_logo ?? '', $baseUrl);
    $userImageBase64 = image_to_base64($userImage, $baseUrl);
    $qrCodeBase64 = $useData && !empty($cardSettings->qr_code) ? image_to_base64($cardSettings->qr_code) : '';

    $html = '
    <div style="width: 500px; box-sizing: border-box;">
        <div style="width: 100%; box-sizing: border-box;">
            <!-- Front of Card -->
            <div style="width: 100%; box-sizing: border-box; background: #fff; border-radius: 8px; overflow: hidden;">
                <!-- Header -->
                <div style="display: flex; align-items: center; padding: 8px 12px; background: #fff; border-bottom: 1px solid #e5e7eb; box-sizing: border-box; text-align: center;">
                    <div class="card-preview-logo-wrapper" style="flex-shrink: 0; margin-right: 12px;">
                        <img src="'.$logoBase64.'" alt="Logo" style="max-width: 50px; max-height: 40px; width: auto; height: auto; object-fit: contain;">
                    </div>
                    <div class="card-preview-title-wrapper" style="flex: 1; text-align: center;">
                        <div style="font-size: clamp(16px, 2.2vw, 20px); font-weight: bold; margin-bottom: 2px; word-wrap: break-word;">'.$defaultClientData->client_name.'</div>
                        <p style="font-size: clamp(12px, 1.6vw, 14px); font-weight: bold; margin: 0; color: #666;" data-item="card_type">'.$type.' Identification Card</p>
                    </div>
                </div>
                
                <!-- Body Section -->
                <div style="background-color: '.($cardSettings->front_color ?? "#1E40AF").'; color: '.($cardSettings->front_text_color ?? "#ffffff").'; padding: 10px; box-sizing: border-box;">
                    <!-- Name and ID Section -->
                    <div style="text-align: center; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                        <div style="font-size: clamp(16px, 2vw, 18px); font-weight: bold; margin-bottom: 4px;">'.($useData ? htmlspecialchars($cardSettings->name) : "Emmanuel Obeng").'</div>
                        <div style="font-size: clamp(13px, 1.6vw, 15px); font-weight: bold;">'.($useData ? htmlspecialchars($cardSettings->unique_id) : "M000000001").'</div>
                    </div>
                    
                    <!-- Content Table: Image, Details, QR Code -->
                    <table style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                        <tr>
                            <!-- Photo Column -->
                            <td style="width: 110px; vertical-align: top; padding-right: 12px;">
                                <div style="width: 110px; height: 110px; background: #fff; padding: 4px; border-radius: 6px; box-sizing: border-box;">
                                    <img src="'.$userImageBase64.'" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; display: block;">
                                </div>
                            </td>
                            
                            <!-- Details Column -->
                            <td style="vertical-align: top; padding: 0 12px;">
                                <table style="width: 100%; border-collapse: collapse; border-spacing: 0; font-size: 12px; line-height: 1.4;" data-item="front_card_details">
                                    <tr>
                                        <td style="color: '.($cardSettings->front_text_color ?? "#ffffff").'; font-weight: bold; white-space: nowrap; padding-right: 12px; padding-bottom: 4px; vertical-align: top;">Gender:</td>
                                        <td style="word-break: break-word; overflow-wrap: break-word; padding-bottom: 4px; vertical-align: top;">'.($useData ? htmlspecialchars($cardSettings->gender) : "Male").'</td>
                                    </tr>
                                    <tr>
                                        <td style="color: '.($cardSettings->front_text_color ?? "#ffffff").'; font-weight: bold; white-space: nowrap; padding-right: 12px; padding-bottom: 4px; vertical-align: top;">Date of Birth:</td>
                                        <td style="word-break: break-word; overflow-wrap: break-word; padding-bottom: 4px; vertical-align: top;">'.($useData ? (empty($cardSettings->date_of_birth) ? "N/A" : emptyForFalseDate($cardSettings->date_of_birth)) : "1990-01-01").'</td>
                                    </tr>
                                    <tr>
                                        <td style="color: '.($cardSettings->front_text_color ?? "#ffffff").'; font-weight: bold; white-space: nowrap; padding-right: 12px; padding-bottom: 4px; vertical-align: top;">Admission:</td>
                                        <td style="word-break: break-word; overflow-wrap: break-word; padding-bottom: 4px; vertical-align: top;">'.($useData ? (empty($cardSettings->enrollment_date) ? "N/A" : emptyForFalseDate($cardSettings->enrollment_date)) : "1990-01-01").'</td>
                                    </tr>
                                    <tr>
                                        <td style="color: '.($cardSettings->front_text_color ?? "#ffffff").'; font-weight: bold; white-space: nowrap; padding-right: 12px; padding-bottom: 4px; vertical-align: top;">'.$type.' Type:</td>
                                        <td style="word-break: break-word; overflow-wrap: break-word; padding-bottom: 4px; vertical-align: top;">'.($useData ? (empty($cardSettings->day_boarder) ? "Regular" : htmlspecialchars($cardSettings->day_boarder)) : "Regular").'</td>
                                    </tr>
                                    '.($useData && !empty($cardSettings->house) ? '
                                    <tr>
                                        <td style="color: '.($cardSettings->front_text_color ?? "#ffffff").'; font-weight: bold; white-space: nowrap; padding-right: 12px; padding-bottom: 4px; vertical-align: top;">House:</td>
                                        <td style="word-break: break-word; overflow-wrap: break-word; padding-bottom: 4px; vertical-align: top;">'.htmlspecialchars($cardSettings->house).'</td>
                                    </tr>
                                    ' : '').'
                                </table>
                            </td>
                            
                            <!-- QR Code Column -->
                            <td style="width: 90px; vertical-align: top; padding-left: 12px;">
                                <div style="width: 110px; height: 110px; background: #fff; padding: 4px; border-radius: 6px; box-sizing: border-box; text-align: center;">
                                    '.($useData && !empty($qrCodeBase64) ? '<img src="'.$qrCodeBase64.'" style="width: 100%; height: 100%; object-fit: contain; border-radius: 4px; display: block;">' : '<div style="font-size: 10px; color: #666; text-align: center; padding: 5px; line-height: 80px;">QR Code</div>').'
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Footer -->
                <div style="text-align: center; padding: 7px 1px; background: #fff; font-size: 12px; border-top: 1px solid #e5e7eb; box-sizing: border-box;">
                    Valid: '.date('M Y', strtotime($start)).' - '.date('M Y', strtotime($end)).'
                </div>
            </div>
        </div>
    </div>
    <div class="page_break"></div>
    <div style="max-width: 470px; height: 285px; box-sizing: border-box;">
        <div style="width: 100%; box-sizing: border-box;">
            <!-- Back of Card -->
            <div style="width: 100%; box-sizing: border-box; background-color: '.($cardSettings->back_color ?? "#DC2626").'; color: '.($cardSettings->back_text_color ?? "#ffffff").'; padding: 15px; border-radius: 10px; min-height: 285px; border: solid 1px #ccc;">
                <div style="box-sizing: border-box;">
                    <div style="text-align: center; font-size: clamp(16px, 2.2vw, 20px); padding: 10px; background: rgba(255, 255, 255, 0.1); border-radius: 6px; margin-bottom: 15px; word-wrap: break-word; box-sizing: border-box;">
                        <div>'.htmlspecialchars($defaultClientData->client_name ?? "").'</div>
                    </div>
                    <div style="text-align: center; padding: 30px 0; font-size: clamp(13px, 1.8vw, 15px); line-height: 1.6; word-wrap: break-word; box-sizing: border-box; width: 80%; margin: auto auto;">
                        '.htmlspecialchars($cardSettings->back_found_message ?? card_found_message($defaultClientData->client_name)).'
                    </div>
                    <div style="text-align: center; font-size: clamp(12px, 1.6vw, 14px); border-radius: 8px; padding: 12px; background: rgba(255, 255, 255, 0.1); word-wrap: break-word; box-sizing: border-box;">
                        <div style="margin-bottom: 8px; font-weight: bold;">If Found, Contact:</div>
                        <div>'.htmlspecialchars($defaultClientData->client_contact ?? "").'</div>
                    </div>
                </div>
            </div>
        </div>
    </div>'.(!empty($cardSettings->page_break) ? '<div class="page_break"></div>' : '').'';

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