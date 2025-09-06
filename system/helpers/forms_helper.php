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
    
    // Break times
    $first_break_start = $timetable_data['first_break_starts'];
    $first_break_end = $timetable_data['first_break_ends'];
    $second_break_start = $timetable_data['second_break_starts'];
    $second_break_end = $timetable_data['second_break_ends'];
    
    // Calculate time slots with breaks
    $time_slots = [];
    $current_time = strtotime($start_time);
    
    for ($i = 0; $i < $slots; $i++) {
        $slot_start = date('H:i', $current_time);
        $slot_end = date('H:i', strtotime("+{$duration} minutes", $current_time));
        
        // Check if this slot conflicts with breaks
        if (($slot_start >= $first_break_start && $slot_start < $first_break_end) ||
            ($slot_end > $first_break_start && $slot_end <= $first_break_end)) {
            // Skip to end of first break
            $current_time = strtotime($first_break_end);
            $slot_start = date('H:i', $current_time);
            $slot_end = date('H:i', strtotime("+{$duration} minutes", $current_time));
        }
        
        if (($slot_start >= $second_break_start && $slot_start < $second_break_end) ||
            ($slot_end > $second_break_start && $slot_end <= $second_break_end)) {
            // Skip to end of second break
            $current_time = strtotime($second_break_end);
            $slot_start = date('H:i', $current_time);
            $slot_end = date('H:i', strtotime("+{$duration} minutes", $current_time));
        }
        
        $time_slots[] = [
            'start' => date('h:i A', strtotime($slot_start)),
            'end' => date('h:i A', strtotime($slot_end)),
            'slot_number' => $i + 1
        ];
        
        $current_time = strtotime("+{$duration} minutes", strtotime($slot_start));
    }
    
    // Generate HTML table
    $html = '<table class="table table-bordered timetable-table">';
    
    // Table header
    $html .= '<thead><tr>';
    $html .= '<th style="background-color: #f8f9fa; padding: 10px; text-align: center; font-weight: bold;"></th>';
    
    foreach ($time_slots as $slot) {
        $html .= '<th style="background-color: #f8f9fa; padding: 8px; text-align: center; font-size: 12px; min-width: 120px;">';
        $html .= $slot['start'] . '<br>' . $slot['end'];
        $html .= '</th>';
    }
    $html .= '</tr></thead>';
    
    // Table body
    $html .= '<tbody>';
    foreach ($days as $day) {
        $html .= '<tr>';
        $html .= '<td style="background-color: #e8f5e8; padding: 15px; text-align: center; font-weight: bold; width: 80px;">';
        $html .= substr($day, 0, 3); // Show first 3 letters of day
        $html .= '</td>';
        
        foreach ($time_slots as $slot) {
            $slot_id = strtolower($day) . '_' . $slot['slot_number'];
            $html .= '<td style="padding: 20px; text-align: center; vertical-align: middle; min-height: 60px;" ';
            $html .= 'id="' . $slot_id . '" data-day="' . strtolower($day) . '" data-slot="' . $slot['slot_number'] . '">';
            $html .= '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    
    $html .= '</table>';
    
    return $html;
}