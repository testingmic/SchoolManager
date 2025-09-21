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