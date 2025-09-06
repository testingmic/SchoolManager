<?php 
/**
 * Time Slots Builder
 * 
 * @param string $start_time
 * @return string
 */
function time_slots_builder($name, $value = '') {
    $label = ucwords(str_replace("_", " ", $name));
    $html = '<div class="input-group mb-3" title="Start time for lesson each day.">
            <div class="input-group-prepend">
                <span class="input-group-text">'.$label.' <span class="required">*</span></span>
            </div>
            <input max="22:00" type="time" value="'.($value).'" class="form-control" style="border-radius:0px; height:42px;" name="'.$name.'" id="'.$name.'">
        </div>';
    return $html;
}

/**
 * Draw Timetable Table
 * 
 * @param object $timetable_data - Timetable configuration data
 * @param string $start_time - Start time (default: 08:00)
 * @return string - HTML table
 */
function draw_timetable_table($timetable_data, $start_time = '08:00') {
    // Parse expected days
    $days = json_decode($timetable_data['expected_days'], true);
    $slots = (int)$timetable_data['slots'];
    $duration = (int)$timetable_data['duration']; // in minutes

    $allocations = $timetable_data['timetable_allocations'] ?? [];
    
    // Break times
    $first_break_start = $timetable_data['first_break_starts'];
    $first_break_end = $timetable_data['first_break_ends'];
    $second_break_start = $timetable_data['second_break_starts'];
    $second_break_end = $timetable_data['second_break_ends'];
    
    // Calculate time slots with breaks
    $time_slots = [];
    $break_columns = [];
    $current_time = strtotime($start_time);
    $column_index = 0;
    
    for ($i = 0; $i < $slots; $i++) {
        $slot_start = date('H:i', $current_time);
        $slot_end = date('H:i', strtotime("+{$duration} minutes", $current_time));
        $is_break_before = false;
        
        // Check if we need to add first break column
        if (($slot_start >= $first_break_start && $slot_start < $first_break_end) ||
            ($slot_end > $first_break_start && $slot_end <= $first_break_end)) {
            
            // Add break column if not already added
            if (!in_array('first_break', $break_columns)) {
                $time_slots[] = [
                    'start' => date('h:i A', strtotime($first_break_start)),
                    'end' => date('h:i A', strtotime($first_break_end)),
                    'slot_number' => 'break_1',
                    'is_break' => true,
                    'break_name' => 'Break'
                ];
                $break_columns[] = 'first_break';
                $column_index++;
            }
            
            // Skip to end of first break
            $current_time = strtotime($first_break_end);
            $slot_start = date('H:i', $current_time);
            $slot_end = date('H:i', strtotime("+{$duration} minutes", $current_time));
            $is_break_before = true;
        }
        
        // Check if we need to add second break column
        if (($slot_start >= $second_break_start && $slot_start < $second_break_end) ||
            ($slot_end > $second_break_start && $slot_end <= $second_break_end)) {
            
            // Add break column if not already added
            if (!in_array('second_break', $break_columns)) {
                $time_slots[] = [
                    'start' => date('h:i A', strtotime($second_break_start)),
                    'end' => date('h:i A', strtotime($second_break_end)),
                    'slot_number' => 'break_2',
                    'is_break' => true,
                    'break_name' => 'Lunch Break'
                ];
                $break_columns[] = 'second_break';
                $column_index++;
            }
            
            // Skip to end of second break
            $current_time = strtotime($second_break_end);
            $slot_start = date('H:i', $current_time);
            $slot_end = date('H:i', strtotime("+{$duration} minutes", $current_time));
            $is_break_before = true;
        }
        
        $time_slots[] = [
            'start' => date('h:i A', strtotime($slot_start)),
            'end' => date('h:i A', strtotime($slot_end)),
            'slot_number' => $i + 1,
            'is_break' => false
        ];
        
        $current_time = strtotime("+{$duration} minutes", strtotime($slot_start));
        $column_index++;
    }
    
    // Generate HTML table
    $html = '<div id="allocate_dynamic_timetable">';
    $html .= '<table class="table table-bordered timetable-table">';
    
    // Table header
    $html .= '<thead><tr>';
    $html .= '<th style="background-color: #f8f9fa; padding: 0px; text-align: center;"></th>';
    
    foreach ($time_slots as $slot) {
        if ($slot['is_break']) {
            $html .= '<th class="break-column" style="background-color: #ffeaa7; padding: 5px; text-align: center; font-size: 13px; min-width: 60px; color: #2d3436;">';
            $html .= $slot['break_name'] . '<br>';
            $html .= '<small>(' . $slot['start'] . ' - ' . $slot['end'] . ')</small>';
        } else {
            $html .= '<th class="celler" style="padding: 5px; font-weight: normal; color: #000000; font-size: 13px;">';
            $html .= $slot['start'] . '<br>' . $slot['end'];
        }
        $html .= '</th>';
    }
    $html .= '</tr></thead>';
    
    // Table body
    $html .= '<tbody>';
    foreach ($days as $key => $day) {
        $key = $key + 1;
        $html .= '<tr>';
        $html .= '<td class="celler day" style="background-color: #e8f5e8; padding: 15px; text-align: center; width: 80px;">';
        $html .= substr($day, 0, 3); // Show first 3 letters of day
        $html .= '</td>';
        
        foreach ($time_slots as $slot) {
            $slot_key = $key . "_" . $slot['slot_number'];
            if ($slot['is_break']) {
                $html .= '<td class="break-column break-cell blocked" style="background-color: #ffeaa7; padding: 10px; text-align: center; vertical-align: middle; min-width: 60px; min-height: 60px; color: #636e72; font-style: italic;">';
                $html .= $slot['break_name'];
            } else {
                $slot_id = strtolower($day) . '_' . $slot['slot_number'];
                $html .= '<td valign="middle" class="celler" id="'.$slot_key.'" style="text-align: center; padding: 5px; vertical-align: middle; min-height: 60px;" ';
                $html .= 'id="' . $slot_id . '" data-day="' . strtolower($day) . '" data-slot="' . $slot['slot_number'] . '" data-slot_key="' . $slot_key . '">';
                $html .= "<div class='d-flex align-items-center justify-content-center' style='font-size: 13px;' class='w-100 font-bold h-100' id='{$slot_key}' data-slot_item='column'>";
                foreach(($allocations[$slot_key] ?? []) as $allocation) {
                    $html .= "<div class='course_holder'>".$allocation->course_name . " (" . $allocation->course_code . ")<br></div>";
                }
                $html .= "</div>";
            }
            $html .= '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';

    
    return $html;
}