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