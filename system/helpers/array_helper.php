<?php 
/**
 * Reassign the category id as the Key and Return the new Array list
 * 
 * @param Array $category_list
 * 
 * @return Array
 */
function filter_fees_category($category_list = []) {
    // set a new variable
    $new_category = [];
    foreach($category_list as $category) {
        $new_category[$category["id"]] = $category;
    }
    return $new_category;
}
?>