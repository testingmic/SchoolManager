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
            <i class="fas fa-user-graduate card-icon col-green"></i>
            <div class="card-wrap">
                <div class="padding-20">
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
            <i class="fas fa-user-tie card-icon col-orange"></i>
            <div class="card-wrap">
                <div class="padding-20">
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
            <i class="fas fas fa-user-check card-icon col-blue card-icon"></i>
            <div class="card-wrap">
                <div class="padding-20">
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
                    <i class="fas fa-ticket-alt card-icon col-green"></i>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 data-attendance_count="ActiveSchoolDays" class="font-light mb-0">0</h3>
                                <span class="text-black font-13">Active Days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    }
    return $html;
}
?>