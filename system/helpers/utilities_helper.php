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
?>