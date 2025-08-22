<?php 

/** 
 * Colors for the Files
 * 
 * @param $ext
 * 
 * @return String
 * 
 **/
function item_color($ext) {
    // default
    $color = 'danger';
    // //: Background color of the icon
    if(in_array($ext, ['doc', 'docx'])) {
        $color = 'primary';
    } elseif(in_array($ext, ['xls', 'xlsx', 'csv'])) {
        $color = 'success';
    } elseif(in_array($ext, ['txt', 'json', 'rtf', 'sql', 'css', 'php'])) {
        $color = 'default';
    }

    return $color;
}

/** 
 * Just Return the String just as it is
 * 
 * @param $string
 * 
 * @return String
 * 
 **/
function isNull($string) {
    return $string;
}

/** 
 * Set the Receipt Record Data
 * 
 * @param Object $data
 * @param Object $record
 * @param String $item_id
 * 
 * @return String
 * 
 **/
function fees_receipt_data($data, $record, $item_id, $width = "fees_receipt") {

    // initial variables
    $total_amount = 0;
    $items_list = null;

    // set the receipt data
    $html = '
    <div class="'.$width.'">
        <div class="invoice">
            <div class="invoice-print">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="invoice-title">
                            <h2>Receipt</h2>
                            <div class="invoice-number">#'.$record->receipt_id.'</div>
                        </div>
                        <hr class="pb-0 mb-2 mt-0">
                        <div class="row">
                            <div class="col-md-6">
                                <address>
                                    <strong>Student Details:</strong><br>
                                    '.$record->student_info->name.'<br>
                                    '.$record->student_info->unique_id.'<br>
                                    '.$record->class_name.'<br>
                                    '.$record->department_name.'<br>
                                </address>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <address>
                                <strong>Billed To:</strong><br>
                                '.(!empty($record->student_info->guardian_id[0]->fullname) ? $record->student_info->guardian_id[0]->fullname : null).'
                                '.(!empty($record->student_info->guardian_id[0]->address) ? "<br>" . $record->student_info->guardian_id[0]->address : null).'
                                '.(!empty($record->student_info->guardian_id[0]->contact) ? "<br>" . $record->student_info->guardian_id[0]->contact : null).'
                                '.(!empty($record->student_info->guardian_id[0]->email) ? "<br>" . $record->student_info->guardian_id[0]->email : null).'
                                </address>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <address>
                                <strong>Payment Method:</strong><br>
                                <strong>'.$record->payment_method.'</strong><br>
                                '.(
                                    $record->payment_method === "Cheque" ? 
                                    "<strong>".explode("::", $record->cheque_bank)[0]."</strong><br>
                                    <strong>#{$record->cheque_number}</strong>" : ""    
                                ).'
                                </address>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <address>
                                <strong>Payment Date:</strong><br>
                                '.$record->recorded_date.'<br><br>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="section-title">Payment Summary</div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-md">
                                <tbody>
                                    <tr>
                                        <th data-width="40" style="width: 40px;">#</th>
                                        <th>Item</th>
                                        <th class="text-right">Amount</th>
                                    </tr>';
                                    foreach($data as $key => $fee) {
                                        $key++;
                                        $total_amount += $fee->amount;

                                        $html .= "
                                        <tr>
                                            <td data-width=\"40\" style=\"width: 40px;\">{$key}</td>
                                            <td>".(!empty($fee->category_name) ? $fee->category_name : $fee->category_id)."</td>
                                            <td class=\"text-right\">{$fee->amount}</td>
                                        </tr>";
                                    }
                                $html .= '
                                    </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 text-right">
                                <div class="invoice-detail-item">
                                    <div class="invoice-detail-name">Total</div>
                                    <div class="invoice-detail-value invoice-detail-value-lg">'.number_format($total_amount, 2).'</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-md-right">
                <button onclick="return print_receipt(\''.$item_id.'\')" class="btn btn-warning btn-icon icon-left"><i class="fas fa-print"></i> Print Receipt</button>
            </div>
        </div>
    </div>';

    return $html;
}

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

/**
 * Reassign the course id as the Key and Return the new Array list
 * 
 * @param Array $timetable_list
 * 
 * @return Array
 */
function format_timetable($timetable_list) {
    // set a new variable
    $new_timetable = [];
    foreach($timetable_list as $timetable) {
        $new_timetable[$timetable["course_id"]] = $timetable;
    }
    return $new_timetable;
}

/**
 * correctly format the payment information
 * 
 * @param $payment_data
 * 
 * @return Array
 */
function format_payment_data($payment_data) {

    // global variables
    global $myClass;

    // init array
    $array = [];

    // if the payment data was set
    if(isset($payment_data[2])) {
        $split = explode("/", $payment_data[2]);
        $module = $myClass->stringToArray($split[1]);
        $months = $myClass->stringToArray($split[2]);
        $amount = $myClass->stringToArray($split[3]);
        $category = $myClass->stringToArray($split[0]);

        // loop through the categories
        foreach($category as $key => $cat) {
            $array[$cat][$months[$key]] = $amount[$key];
        }

        return $array;
    }
}

/**
 * List the Student Attendance
 * 
 * @return String
 */
function format_daily_attendance($logs, $student_id, $student_name = null) {
    $mylist = null;
    $colors = [
        "late" => "bg-warning",
        "present" => "bg-success",
        "absent" => "bg-danger",
        "holiday" => "bg-info",
        "leave" => "bg-secondary",
        "late_with_permission" => "bg-warning",
        "late_without_permission" => "bg-warning",
    ];
    // if the item is an array and also not empty
    if(is_array($logs) && !empty($logs)) {
        // loop through the logs list
        foreach($logs as $date => $log) {
            $state = str_ireplace("_", " ", $log["status"]);
            $state_color = $colors[$log["status"]] ?? "bg-success";

            $mylist .= "<span onclick='return show_Attendance_Log(\"{$student_id}\",\"{$date}\",\"{$student_name}\");' class='attendance_log {$state_color}'>{$date}<br><strong>{$state}</strong></span>";
        }
    }

    return $mylist;
}

/**
 * Format the Student Grade Result
 * 
 * @return Array
 */
function format_student_grade($logs, $student_id, $student_name = null, $grade_type = null) {
    $mylist = null;
    $mymarks = [];
    $actualmarks = [];

    if(!empty($logs) && is_array($logs)) {
        if(isset($logs["dates"])) {
            foreach($logs["dates"] as $date => $log) {
                $_date = date("jS M", strtotime($date));
                $mylist .= "
                <span item_date=\"{$date}\" onclick='return show_Grading_Log(\"{$student_id}\",\"{$date}\",\"{$grade_type}\",\"{$student_name}\");' class='grading_log'>
                    {$_date}<br>
                    Score: <strong class='font-17'>{$log["grade"]}</strong>
                </span>";
                $mymarks[] = $log["grade"];
                $actualmarks[] = $log["grading"] ?? 35;
            }
        }
    }

    return [
        "the_list" => $mylist,
        "the_marks" => $mymarks,
        "actualmarks" => $actualmarks
    ];
}

/**
 * Format the Document List
 *
 * @return String
 **/
function format_directory_item($value, $allow_click = true, $no_container = false) {
    global $myClass;

    $item_name = $myClass->remove_quotes($value->name);
    $html = (!$no_container ? "<div class='col-lg-4 col-md-6 col-sm-6 p-1' data-element_type='folder' data-element_id='{$value->item_id}'>" : null);
    $html .= "<div class='document-container p-2' data-parameter_type='folder' data-parameter_url='{$value->item_id}' data-parameter_name='{$item_name}' data-parameter='document'>
            <div class='d-flex justify-content-start'>
                <div class='mr-2'>
                    <i ".($allow_click ? "onclick='return load_document(\"{$value->item_id}\")' title='Click to Open'" : null)." class='fa open-once ".($allow_click ? "cursor" : null)." fa-folder fa-2x'></i>
                </div>
                <div title='Double Click to Open: {$item_name}' class='folder-title'>
                    <span>{$value->name}</span>
                </div>
            </div>
        </div>";
    $html .= (!$no_container ? "</div>" : null);

    return $html;
}

/**
 * Get the file from the list
 * 
 * @param Array     $files_list
 * @param String    $file_ref_id
 *
 * @return Object
 **/
function document_file($files_list, $file_ref_id) {
    if(is_array($files_list)) {
        foreach ($files_list as $key => $value) {
            if($value["unique_id"] === $file_ref_id) {
                return $value;
            }
        }
    }
}

/**
 * Format the Document List
 *
 * @return String
 **/
function format_file_item($value, $no_container = false, $isTrash = false) {
    global $myClass;

    $isPDF = in_array($value->file_type, ["pdf"]);
    $item_name = $myClass->remove_quotes($value->name);
    $html = (!$no_container ? "<div class='col-lg-4 col-md-6 col-sm-6 p-1' data-element_type='file' data-element_id='{$value->item_id}'>" : null);
    $html .= "
        <div class='document-container file-container' data-parameter_type='file' data-parameter_url='{$value->item_id}' data-parameter_name='{$item_name}' data-parameter='file'>
            <div class='item_loader'></div>
            <div class='text-center'>
                ".(!empty($isPDF) ? 
                    "<span onclick='return preview_pdf_document(\"{$value->upload_id}_{$value->file_ref_id}\");' class='preview-icon' title='Read File'><i class='fa fa-eye text-warning'></i></span>" : null
                )."
                ".(!empty($value->file_ref_id) ? 
                    "<span onclick='return document_download(\"{$value->item_id}\");' title='Download this File' class='download-icon'><i class='fa fa-download text-success'></i></span>" : null
                )."
                ".(!$isTrash ? 
                    "<span onclick='return load_quick_form(\"document_update_file\",\"{$value->item_id}\")' title='Modify this File' class='edit-icon'><i class='fa fa-edit text-primary'></i></span>" : null
                )."
                <div class='p-2'>
                    <i class='text-{$value->color} {$value->favicon} fa-8x'></i>
                </div>
                <div title='{$item_name}' class='file-title p-2'>
                    <span>{$value->name}</span>
                </div>
            </div>
        </div>";
    $html .= (!$no_container ? "</div>" : null);

    return $html;
}

/**
 * Format the Bus Item
 *
 * @return String
 **/
function format_bus_item($value, $no_container = false, $no_buttons = false, $width = "col-lg-4 col-md-6 col-sm-6", $permissions = []) {
    
    global $myClass;

    $item_name = $myClass->remove_quotes($value->brand);
    $image = "assets/img/placeholder.jpg";

    // get the images
    if(!empty($value->attachment)) {
        $counter = count($value->attachment["files"]);

        foreach($value->attachment["files"] as $img) {
            if(is_file($img["path"]) && file_exists($img["path"])) {
                $image = $img["path"];
                break;
            }
        }
    }
    // set the content
    $html = (!$no_container ? "<div class='{$width} transition-all duration-300 transform hover:-translate-y-1' data-element_type='bus' data-element_id='{$value->item_id}'>" : null);
    $html .= "
    <div class='card'>
        <div class='card-body p-2' data-parameter_type='bus' data-parameter_url='{$value->item_id}' data-parameter='bus'>
            <div class='item_loader'></div>
            <div class='img-container cursor bus-img-container' ".(!$no_buttons && !empty($permissions["hasModify"]) ? "onclick='return load(\"bus/{$value->item_id}\");'" : null).">
                <img src='{$myClass->baseUrl}{$image}' class='bus-img' width='100%'>
            </div>
            <div class='text-left pb-2 pt-2 pr-0 pl-0 description'>
                <div class='mb-2'><span class='font-bold text-primary'>Color:</span><span class='float-right'>".(!empty($value->color) ? "<span style='Background:{$value->color}' class='badge'>&nbsp;</span>" : "-")."</span></div>
                <div class='mb-2'><span class='font-bold text-primary'>Registration Number:</span><span class='float-right'>{$value->reg_number}</span></div>
                <div class='mb-2'><span class='font-bold text-primary'>Purchase Price:</span><span class='float-right'>".(!empty($value->purchase_price) ? number_format($value->purchase_price, 2) : "-")."</span></div>
                <div class='mb-2'><span class='font-bold text-primary'>Year of Purchase:</span><span class='float-right'>{$value->year_of_purchase}</span></div>
                <div class='mb-2'><span class='font-bold text-primary'>Insurance Company:</span><span class='float-right'>{$value->insurance_company}</span></div>
                <div class='mb-2'><span class='font-bold text-primary'>Annual Premium:</span><span class='float-right'>".(!empty($value->annual_premium) ? number_format($value->annual_premium, 2) : "-")."</span></div>
                <div class='mb-2'><span class='font-bold text-primary'>Insurance Date:</span><span class='float-right'>{$value->insurance_date}</span></div>
                <div class='mb-2'><span class='font-bold text-primary'>Date of Expiry:</span><span class='float-right'>".(!empty($value->expiry_date) ? $value->expiry_date : "-")."</span></div>
                <div class='mt-3 border-top pt-2'>
                    <div class='d-flex justify-content-between'>
                        <div class='text-left'>
                            ".(!$no_buttons && !empty($permissions["hasModify"]) ? "<button class='btn btn-sm mb-1 btn-outline-danger' title='Delete {$value->brand}' onclick='return delete_bus(\"{$value->item_id}\",\"{$item_name}\");'><i class='fa fa-trash'></i> Trash</button>" : null)."
                        </div>
                        <div class='text-right'>
                            ".(!empty($permissions["hasModify"]) && $no_buttons ? "<button class='btn btn-sm mb-1 btn-outline-success' title='Update {$value->brand}' onclick='return update_bus(\"{$value->item_id}\");'><i class='fa fa-edit'></i> Edit</button>" : null)."
                            ".(!$no_buttons ? "<button class='btn btn-sm mb-1 btn-outline-success' title='View {$value->brand}' onclick='return load(\"bus/{$value->item_id}\");'><i class='fa fa-eye'></i> View</button>" : null)."
                            ".(!empty($permissions["markAttendance"]) && $permissions["attendancePage"] ? "<button class='btn btn-sm mb-1 btn-outline-primary' title='View {$value->brand}' onclick='return load(\"bus/{$value->item_id}\");'><i class='fa fa-eye'></i> Manage Interactions</button>" : null)."
                            ".(!empty($permissions["markAttendance"]) && !$permissions["attendancePage"] ? "<button class='btn btn-sm mb-1 btn-outline-primary' title='View Comments Log for {$value->brand}' onclick='return load(\"bus/{$value->item_id}/attendance\");'><i class='fa fa-bus'></i> Attendance</button>" : null)."
                        </div>
                    </div>
                </div>
            </div>
            <div title='{$item_name}' class='cursor font-20 bg-primary rounded text-white p-2'>
                <p>{$value->brand}</p>
            </div>
        </div>
    </div>";
    $html .= (!$no_container ? "</div>" : null);

    return $html;
}

/**
 * get the overall summary information of a specific directory
 * 
 * @param Array $documents
 * @param Int   $files_count
 * @param Int   $folders_count
 * @param Int   $folders_size
 * 
 * @return Array
 */
function document_summary($documents, &$files_count = 0, &$folders_count = 0, &$folder_size = 0) {

    $last_updated = null;

    foreach($documents as $key => $docs) {

        if($key === "directory_list") {
            foreach($docs as $idocument) {
                if(isset($idocument->directory_tree) && is_array($idocument->directory_tree)) {
                    foreach($idocument->directory_tree as $tdocument) {
                        $folders_count++;
                        document_summary($tdocument, $files_count, $folders_count, $folder_size);
                    }
                } else {
                    $files_count++;
                    $last_updated = $idocument->last_updated;
                    $folder_size += $idocument->file_size;
                }
            }
        } elseif($key === "file_list") {
            foreach($docs as $idocument) {
                $files_count++;
                $last_updated = $idocument->last_updated;
                $folder_size += $idocument->file_size;
            }
        } else {
            if(isset($docs->directory_tree) && is_array($docs->directory_tree)) {
                foreach($docs->directory_tree as $tdocument) {
                    $folders_count++;
                    document_summary($tdocument, $files_count, $folders_count, $folder_size);
                }
            } else{
                $files_count++;
                $last_updated = $docs->last_updated;
                $folder_size += $docs->file_size;
            }
        }
    }

    return [
        "summary" => [
            "last_updated" => $last_updated,
            "files_count" => $files_count,
            "folder_count" => $folders_count,
            "folder_size" => file_size_convert(round($folder_size * 1024))
        ]
    ];
}

// sort the array
function sort_lesson_start_time($a, $b) {
    return strtotime($a["lesson_start_time"]) - strtotime($b["lesson_start_time"]);
}

/** 
 * Grade Button
 * 
 * @return String
 */
function grading_button($student_id, $student_name, $grade_type) {
    return "<button onclick='return show_Attendance_Grading_Log_Form(\"{$student_id}\",\"{$student_name}\",\"{$grade_type}\",\"grading\");' class='btn btn-secondary font-bold font-14 bg-black pt-1 pb-1'>New</button>";
}
?>