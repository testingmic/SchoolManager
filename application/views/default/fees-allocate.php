<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Allocate Student Fees";
$response->title = $pageTitle;

$response->scripts = ["assets/js/fees_allocation.js", "assets/js/webcam.js"];

// student id
$user_id = $SITEURL[1] ?? null;
$is_new_admission = (bool) (isset($_GET["is_new_admission"]) && ($_GET["is_new_admission"] == 1));

// if the user has the permission to allocate fees
$canAllocate = $accessObject->hasAccess("allocation", "fees");
$hasUpdate = $accessObject->hasAccess("update", "student");

/** confirm that the user has the permission to receive payment */
if(!$canAllocate) {
    $response->html = page_not_found("permission_denied");
} 
// continue if the user has the permission
elseif(!empty($user_id)) {

    // set the student parameter
    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "minified" => "simplified",
        "no_limit" => 1,
        "user_type" => "student"
    ];

    $data = load_class("users", "controllers", $student_param)->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        // set the scholarship status
        $scholarship_status = $data->scholarship_status == 1 ? "<div><span class='badge p-1 badge-success'>Full Scholarship</span></div>" : null;

        // set the parameters
        $feesParam = (object) [
            "client_data" => $defaultUser->client, 
            "userData" => $defaultUser, 
            "student_id" => $data->user_id,
            "clientId" => $defaultUser->client_id,
            "set_category_key" => true
        ];

        // create new object
        $feesObject = load_class("fees", "controllers", $feesParam);

        // get the class and student fees allocation
        $pay_button = true;
        $allocation_list = "";
        $allocationReviewList = null;
        $existing_category = [];
        $studentAllocationArray = [];
        $dont_show_button = false;
        
        $total_amount = 0;
        $total_paid = 0;
        $total_balance = 0;

        $studentAllocation = $feesObject->students_fees_allocation($feesParam)["data"];
        $classAllocation = $feesObject->class_fees_allocation($feesParam)["data"];

        // set the headers
        $headers["balance"] = "BALANCE";

        // set the user_id id in the console
        $response->array_stream['user_id'] = $user_id;

        // list the class allocation
        if((empty($studentAllocation) && !empty($classAllocation)) || !empty($studentAllocation)) {
            
            // convert the student variable into an array list
            foreach($studentAllocation as $item) {
                $studentAllocationArray[$item->category_id] = (array) $item;
            }

            // if the student allocation is empty
            if(empty($studentAllocation)) {
                $headers["balance"] = "PAYABLE";
            }

            // set the allocation list to display
            $allocationReviewList = !empty($studentAllocationArray) ? $studentAllocationArray : $classAllocation;

            // loop through the allocation
            foreach($allocationReviewList as $allocation) {
                
                // init values
                $reverse_button = "hidden";
                $disabled = null;
                $is_exempted = null;
                $idisabled = null;
                $exempt_class = null;
                $request_buttons = null;

                // append to the existing category list
                $existing_category[] = $allocation->category_id ?? $allocation["category_id"];

                // set the allocation
                if(!empty($studentAllocationArray)) {
                    // set variables
                    $allocation = (object) $allocation;
                    $get_match = (array) $allocation;

                    // confirm if the student is exempted to pay for this item
                    $is_exempted = $get_match["exempted"];
                    
                    // set new variables if the user is exempted
                    if($is_exempted) {
                        $reverse_button = null;
                        $exempt_class = "removed";
                        $request_buttons = "hidden";
                        $idisabled = "disabled='disabled'";
                    }
                } else {
                    $get_match = $feesObject->match_allocation($allocation, $studentAllocationArray);
                }

                // initial values
                $amount_paid = 0.00;
                $amount_due = $allocation->amount_due ?? $allocation->amount;
                $balance = $amount_due;

                // set the status
                $status = null;
                $delete_btn = null;

                // set the button
                $save_btn = "<button title='Save this fee allocation.' onclick='return save_category(\"{$allocation->category_id}\",\"{$user_id}\")' data-save_category='{$allocation->category_id}' class='btn btn-sm btn-outline-success {$request_buttons}'><i class='fa fa-save'></i></button>";
                $delete_btn = "&nbsp;<button title='Remove this item from list' onclick='return remove_category(\"{$allocation->category_id}\")' data-remove_category='{$allocation->category_id}' class='btn btn-sm btn-outline-danger {$request_buttons}'><i class='fa fa-ban'></i></button>";
                $undo_button = "&nbsp;<button title='Reverse the removal of this category list' onclick='return reverse_action(\"{$allocation->category_id}\")' data-reverse_action='{$allocation->category_id}' class='btn btn-sm {$reverse_button} btn-outline-warning'><i class='fa fa-reply-all'></i></button>";

                // set a new status
                if(empty($studentAllocationArray)) {
                    // set the button
                    $pay_button = false;
                    $dont_show_button = true;

                    // append to the list
                    $save_btn = "&nbsp;<button title='Save this fee allocation.' onclick='return save_category(\"{$allocation->category_id}\",\"{$user_id}\")' data-save_category='{$allocation->category_id}' class='btn btn-sm btn-outline-success'><i class='fa fa-save'></i></button>";
                    $status = "<span class='p-1 badge badge-primary'>Not Set</span>";
                } elseif(!empty($get_match)) {

                    // get the amount paid
                    $amount_paid = $get_match["amount_paid"];
                    $balance = $get_match["balance"];

                    // if the paid_status is equal to one
                    if($get_match["paid_status"] === 1) {
                        $undo_button = null;
                        $delete_btn = null;
                        $save_btn = null;
                        $disabled = "disabled='disabled'";
                        $status = "<span class='p-1 badge badge-success'>Paid</span>";
                    } else if($get_match["paid_status"] === 2) {
                        $dont_show_button = true;
                        $delete_btn = null;
                        $status = "<span class='p-1 badge badge-primary'>Part Payment</span>";
                    } else if($get_match["paid_status"] === 0) {
                        $dont_show_button = true;
                        $status = "<span class='p-1 badge badge-danger'>Not Paid</span>";
                    }
                    $status = $is_exempted ? "<span class='p-1 badge badge-dark'>Exempted</span>" : $status;
                }

                // increment the values
                $total_balance += $balance;
                $total_paid += $amount_paid;
                $total_amount += $allocation->amount_due ?? $allocation->amount;
                
                // set final varialbes
                $b = "<div class='request_buttons' data-category_id='{$allocation->category_id}'>";
                $e = "</div><div data-category_id='{$allocation->category_id}' class='request_loader hidden'><i class='fa fa-spin fa-spinner'></i></div>";

                // append to the list
                $allocation_list .= "
                    <tr data-row_id='{$allocation->category_id}'>
                        <td><span class='font-18'>".($allocation->category_name ?? ($allocation->category_list ?? null))."</span><br>{$status}</td>
                        <td><input style='min-width:90px' value='{$amount_due}' disabled class='form-control font-weight-bold font-17 text-center'></td>
                        <td><input style='min-width:90px' value='{$amount_paid}' disabled class='form-control font-17 text-center'></td>
                        <td><input style='min-width:90px' min='0' max='{$amount_due}' value='{$balance}' {$idisabled} ".($disabled ? $disabled : "data-item='amount' data-category_id='{$allocation->category_id}'")." class='form-control font-weight-bold font-17 p-0 text-center {$exempt_class}' type='number'></td>
                        <td class='text-center'>{$b}{$save_btn}{$delete_btn}{$undo_button}{$e}</td>
                    </tr>";
            }

        }

        // If this is a new addition and the fees allocation is not empty
        if(!empty($allocationReviewList) && $is_new_admission) {

            // fees category list
            $feesCategoryList = $myClass->pushQuery("id, name, amount", "fees_category", "client_id='{$clientId}' AND status='1'");
            
            // if the user is a new student
            foreach($feesCategoryList as $category) {

                // if the category is not found in the already existing list
                if(!in_array($category->id, $existing_category)) {

                    // set new variables
                    $b = "<div class='request_buttons' data-category_id='{$category->id}'>";
                    $e = "</div><div data-category_id='{$category->id}' class='request_loader hidden'><i class='fa fa-spin fa-spinner'></i></div>";

                    // set additional buttons
                    $save_btn = "&nbsp;<button title='Save this fee allocation.' onclick='return save_category(\"{$category->id}\",\"{$user_id}\")' data-save_category='{$category->id}' class='btn btn-sm btn-outline-success'><i class='fa fa-save'></i></button>";
                    $delete_btn = "&nbsp;<button title='Remove this item from list' onclick='return remove_category(\"{$category->id}\")' data-remove_category='{$category->id}' class='btn btn-sm btn-outline-danger'><i class='fa fa-ban'></i></button>";
                    $undo_button = "&nbsp;<button title='Reverse the removal of this category list' onclick='return reverse_action(\"{$category->id}\")' data-reverse_action='{$category->id}' class='btn btn-sm hidden btn-outline-warning'><i class='fa fa-reply-all'></i></button>";
                    
                    // set the status
                    $total_amount += $category->amount;
                    $total_balance += $category->amount;
                    $status = "<span class='p-1 badge badge-primary'>Not Set</span>";

                    // append to the list
                    $allocation_list .= "
                    <tr data-row_id='{$category->id}'>
                        <td><span class='font-18'>{$category->name}</span><br>{$status}</td>
                        <td><input style='min-width:90px' value='{$category->amount}' disabled class='form-control font-weight-bold font-17 text-center'></td>
                        <td><input style='min-width:90px' value='0' disabled class='form-control font-17 text-center'></td>
                        <td><input style='min-width:90px' min='0' value='{$category->amount}' data-item='amount' data-category_id='{$category->id}' class='form-control font-weight-bold font-17 p-0 text-center' type='number'></td>
                        <td class='text-center'>{$b}{$save_btn}{$delete_btn}{$undo_button}{$e}</td>
                    </tr>";
                }
            }

        }

        // if the fees has been allocated to the user
        $allocation_list .= "
        <tr>
            <td><span class='font-18 font-bold'>TOTAL</td>
            <td><input style='min-width:90px' value='{$total_amount}' readonly class='form-control bg-dark text-white font-weight-bold font-20 text-center'></td>
            <td><input style='min-width:90px' value='{$total_paid}' readonly class='form-control bg-dark text-white font-20 text-center'></td>
            <td><input style='min-width:90px' min='0' data-input_function='total_amount' max='{$total_balance}' readonly value='{$total_balance}' class='form-control bg-dark text-white font-weight-bold font-20 p-0 text-center' type='number'></td>
            <td class='text-center'>-</td>
        </tr>";

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'students">Students</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="card author-box pt-2">
                            <div class="card-body pl-1 pr-1">
                                <div class="text-center">
                                    <div class="author-box-center m-0 p-0 flex justify-center">
                                        <img id="avatar" alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                                    </div>
                                    '.($hasUpdate ?
                                        '<style>
                                            #video, canvas {
                                                display: inline-block;
                                                margin-bottom: 10px;
                                            }
                                            #video, #canvas {
                                                display: none;
                                            }
                                        </style>
                                        <video id="video" width="320" height="240" autoplay></video>
                                        <canvas id="canvas" width="320" height="240"></canvas>
                                        <div class="text-center mt-1">
                                            <button class="btn btn-outline-primary" id="replaceAvatar"><i class="fa fa-camera"></i> Change Image</button>
                                            <button class="btn btn-outline-success" id="capture" style="display:none;">Capture Photo</button>
                                            <button class="btn btn-success" id="save" style="display:none;"><i class="fa fa-save"></i> Save Photo</button>
                                        </div>' : null
                                    ).'
                                </div>
                                <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">
                                    '.$data->name.'
                                    '.$scholarship_status.'
                                </div>
                                <div class="text-center border-top mt-0">
                                    <div class="author-box-description font-22 text-success font-weight-bold">'.$data->unique_id.'</div>
                                    <div class="author-box-description font-22 text-info font-weight-bold mt-1">'.$data->class_name.'</div>
                                    <div class="w-100 mt-2 border-top pt-3">
                                        <a class="btn mb-2 btn-dark" href="'.$baseUrl.'student/'.$user_id.'"><i class="fa fa-arrow-circle-left"></i> Go Back</a>
                                        '.(!empty($allocationReviewList) ? '
                                            '.($pay_button ? 
                                                '&nbsp;
                                                <a href="'.$myClass->baseUrl.'fees-payment?student_id='.$user_id.'&class_id='.$data->class_id.'" class="btn mb-2 btn-outline-success">
                                                    <i class="fa fa-adjust"></i> PAY FEES
                                                </a>' : null
                                            ).'' : null
                                        ).'
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 col-lg-8">
                        <div class="card">
                            '.(!empty($allocationReviewList) ? '
                                <div class="card-header text-uppercase">Fees Allocation Table</div>
                                    <div class="card-body" id="fees_allocation_table">
                                        <div class="form-content-loader" style="display: none; position: absolute">
                                            <div class="offline-content text-center">
                                                <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            '.($dont_show_button ? '
                                                <button onclick="return save_student_bill(\''.$user_id.'\',\''.$myClass->remove_quotes($data->name).'\');" title="Click to save student bill." class="btn mb-2 btn-outline-primary"><i class="fa fa-save"></i> SAVE BILL</button>&nbsp;
                                            ' : null).'
                                            '.($studentAllocationArray ? 
                                                '<a href="'.$baseUrl.'download/student_bill/'.$user_id.'?print=1" target="_blank" class="btn mb-2 btn-outline-warning"><i class="fa fa-print"></i> PRINT BILL</a>' : null
                                            ).'
                                        </div>
                                        <div class="mt-3 border-top pt-3">
                                            <div class="table-responsive">
                                                <input type="hidden" disabled id="is_new_admission" name="is_new_admission" value="'.$is_new_admission.'">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="mb-3 font-weight-bold">CATEGORY</th>
                                                            <th width="18%" class="mb-3 font-weight-bold">DUE</th>
                                                            <th width="18%" class="mb-3 font-weight-bold">PAID</th>
                                                            <th width="17%" class="mb-3 font-weight-bold">'.$headers["balance"].'</th>
                                                            <th width="15%" class="mb-3 font-weight-bold"></th>                    
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        '.$allocation_list.'
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>' : 
                            notification_modal("Fees Category Not Set", $myClass->error_logs["fees_category_not_set"]["msg"], $myClass->error_logs["fees_category_not_set"]["link"])
                        ).'
                        </div>
                    </div>
                </div>
            </div>
        </section>';
    }

}

// print out the response
echo json_encode($response);
?>