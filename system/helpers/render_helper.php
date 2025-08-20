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

?>