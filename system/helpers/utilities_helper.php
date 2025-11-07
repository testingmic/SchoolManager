<?php
/**
 * Remove any weekends and dates exceeding current day
 * 
 * @param array $dates
 * 
 * @return array
 */
function filterWeekendDates(array $dates): array {
    $today = new DateTime(); // Current date

    return array_values(array_filter($dates, function($date) use ($today) {
        $d = new DateTime($date);

        // Skip if greater than today
        if ($d > $today) {
            return false;
        }

        // Skip weekends (Saturday = 6, Sunday = 0)
        $dayOfWeek = (int) $d->format('w');
        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            return false;
        }

        return true;
    }));
}

function default_mixpanel_init() {
    return "
    autocapture: true,
    record_sessions_percent: 100,
    track_pageview: true,
    mask_all_text: false,
    mask_all_inputs: false,
    mask_all_element_attributes:  false,
    persistence: 'localStorage',
    timestamp: '".date("Y-m-d H:i:s")."',
    track_links: true,
    track_forms: true,
    autotrack: true,
    property_blacklist: [],
    debug: false,
    secure_cookie: true,
    ip: true,
    record_block_class: 'mp-block',
    record_mask_text_selector: '.mp-mask, input[type=\"password\"]',
    record_collect_fonts: true,
    record_idle_timeout_ms: 20 * 60 * 1000,
    record_max_ms: 1 * 60 * 60 * 1000";
}

/**
 * Render the course
 * 
 * @param object $course
 * @param object $courseObject
 * @param boolean $hasUpdate
 * 
 * @return string
 */
function course_renderer($course, $courseObject = null, $hasUpdate = false) {
    
    $action = "<div>
        <a href='#' onclick='return load(\"course/{$course->id}\");' class='btn btn-sm btn-outline-primary'>
            <i class='fa fa-eye'></i> View
        </a>
    </div>";

    if($hasUpdate) {
        $action .= "
        <div>
            <a href='#' onclick='return delete_record(\"{$course->id}\", \"{$courseObject['item_type']}\", \"delete\", \"{$courseObject['user_id']}\");' class='btn btn-sm btn-outline-danger'>
                <i class='fa fa-trash'></i> Remove
            </a>
        </div>";
    }

    return '
        <div class="col-lg-6 col-md-6" data-record-row_id="'.$course->id.'" data-row_id="'.$course->id.'">
            <div class="card">
                <div class="card-body pr-2 pl-2 pt-0 pb-0">
                    <div class="pb-0 pt-2">
                        <p class="clearfix mb-2">
                            <span class="float-left bold">Name</span>
                            <span class="float-right text-muted">
                                <span class="user_name" '.(!$courseObject['isWardParent'] && ($courseObject['isTutor'] && in_array($course->id, $courseObject['defaultUser']->course_ids)) ? 'onclick="load(\'course/'.$course->item_id.'\');"' : null).'>
                                    '.($courseObject['isAdmin'] ? "<a href='{$courseObject['myClass']->baseUrl}course/{$course->id}/lessons'>" : null).'
                                        '.$course->name.'
                                    '.($courseObject['isAdmin'] ? "</a>" : null).'
                                </span>
                            </span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left bold">Code</span>
                            <span class="float-right text-muted">'.$course->course_code.'</span>
                        </p>
                        <p class="clearfix hidden">
                            <span class="float-left bold">Credit Hours</span>
                            <span class="float-right text-muted">'.$course->credit_hours.'</span>
                        </p>
                    </div>
                </div>
                <div class="card-footer p-2 d-flex gap-2 justify-content-end">
                    '.$action.'
                </div>
            </div>
        </div>';
}

/**
 * Filter the accounting object
 * 
 * @param object $object
 * 
 * @return object
 */
function filterAccountingObject($object) {
    // list of keys to remove
    $keys_to_remove = [
        "reversed", "status", "attach_to_object", "record_object", 
        "account_bank", "account_number", "student_id", "account_id"
    ];

    // loop through the object
    foreach($keys_to_remove as $key) {
        unset($object->{$key});
    }
    return $object;
}

/**
 * Print the page content
 * 
 * @return string
 */
function print_page_content() {
    return"<script>
    function print_page_content() {
        window.print();
        window.onfocus = (evt) => { window.close(); }
        window.onafterprint = (evt) => { window.close(); }
    }
    print_page_content();
    </script>";
}

/**
 * Format the date to show
 * 
 * @param string $date
 * 
 * @return string
 */
function format_date_to_show($date, $is_null = false) {
    if(empty($data)) {
        return !$is_null ? "N/A" : null;
    }
    $date = date('Y-m-d', strtotime($date));
    if(in_array($date, ["1970-01-01", "01/01/1970"])) {
        return !$is_null ? "N/A" : null;
    }
    return $date;
}
?>