<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Summary Requestion Information";

// access permissions    
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;
$hasIssue = $accessObject->hasAccess("issue", "library");

$tTitle = $hasIssue ? "Issued Books List" : "My Books List";

$response->title = "{$pageTitle} : {$appName}";
// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

// if the user id is not empty
if(!empty($item_id)) {

    // parameters for the category
    $params = (object) ["clientId" => $session->clientId, "show_list" => true, "borrowed_id" => $item_id, "limit" => 1, "userData" => $defaultUser];
    $data = load_class("library", "controllers")->issued_request_list($params);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {
        
        // set the first key
        $data = $data["data"][0];
        $item_param = $data;
        $response->scripts = ["assets/js/library.js", "assets/js/comments.js"];

        // list the books in this request
        $books_list = "";

        // set the permission 
        $isPermitted = (bool) ($hasIssue && $data->state === "Requested");
        $isRequested = (bool) ($data->state === "Requested");
        $isEditable = (bool) !in_array($data->state, ["Cancelled", "Overdue", "Approved"]);
        $isOverdue = (bool) ($data->state === "Overdue");
        $canReturn = (bool) in_array($data->state, ["Approved", "Overdue", "Issued"]);

        // loop through the books list
        foreach($data->books_list as $book) {
            // set the permission
            $books_list .= "<tr class='each_book_item' data-request_id='{$item_id}' data-book_id='{$book->book_id}'>";
            $books_list .= "<td>
                <div class='d-flex justify-content-start'>
                    <div class='mr-2'>".(!empty($book->book_image) ? "<img src='{$baseUrl}{$book->book_image}' width='50px' height='40px'>" : "")."</div>
                    <div><a href='#' onclick='return loadPage(\"{$baseUrl}update-book/{$book->book_id}\");'>{$book->title}</a> <br> <strong>{$book->isbn}</strong></div>
                </div>
            </td>";
            $books_list .= "<td>{$book->author}</td>";

            // if the user has the required permissions
            if($isEditable && $isRequested) {
                $books_list .= "<td><input type='number' min='1' max='{$book->books_stock}' class='form-control' style='width:100px' data-request_id='{$item_id}' data-book_id='{$book->book_id}' data-original='{$book->quantity}' value='{$book->quantity}'></td>";
            } else {
                $books_list .= "<td>{$book->quantity}</td>";
            }

            $books_list .= (round($data->fine) > 2) ? "<td>{$book->fine}</td>" : "";

            // if the user has permission
            if($canReturn) {
                // if the book has not been returned
                if(($book->status === "Borrowed") && $hasIssue) {
                    $books_list .= '<td><button onclick="return return_Requested_Book(\'single_book\',\''.$book->book_id.'\',\''.$book->fine.'\');" title="Return Book" class="btn btn-sm btn-outline-warning"><i class="fa fa-reply"></i> Return</button></td>';
                } else {
                    $books_list .= "<td><span class='badge badge-".(($book->status === "Borrowed") ? "primary" : "success")."'>{$book->status}</span></td>";
                }
            }

            // if the item is not yet overdue
            // $isPermitted && 
            if($isRequested && !$isOverdue) {
                $books_list .= "<td align='center'>";
                $books_list .= "<button onclick=\"return remove_Book('{$item_id}','{$book->book_id}');\" class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></button>";
                $books_list .= "&nbsp;<button onclick=\"return save_Book_Quantity('{$item_id}','{$book->book_id}');\" id='save_book_{$book->book_id}' class='btn btn-sm hidden btn-outline-success'><i class='fa fa-save'></i></button>";
                $books_list .= "</td>";
            }
            $books_list .= "</tr>";
        }

        // books listing
        $books_listing = '
        <div class="table-responsive">
            <table data-empty="" class="table table-striped datatable">
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Quantity</th>
                        '.(round($data->fine) > 1 ? "<th>Fine</th>" : "").'
                        '.($isRequested || $canReturn ? '<th width="13%"></th>' : '').'
                    </tr>
                </thead>
                <tbody>'.$books_list.'</tbody>
            </table>
        </div>';
        
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'issued-books">'.$tTitle.'</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row" id="books_request_details">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>USER INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-3 pt-0">
                                <div class="d-flex justify-content-start">
                                    <div class="mr-2">
                                        <img src="'.$baseUrl.''.$data->user_info->image.'" width="60px">
                                    </div>
                                    <div style="width:100%">
                                        <p class="clearfix">
                                            <span class="float-left">Fullname:</span>
                                            <span class="float-right text-muted">'.($data->user_info->name).'</span>
                                        </p>
                                        <p class="clearfix">
                                            <span class="float-left">Unique ID:</span>
                                            <span class="float-right text-muted">'.($data->user_info->unique_id).'</span>
                                        </p>
                                        <p class="clearfix">
                                            <span class="float-left">Contact:</span>
                                            <span class="float-right text-muted">'.($data->user_info->phone_number).'</span>
                                        </p>
                                        <p class="clearfix">
                                            <span class="float-left">Email:</span>
                                            <span class="float-right text-muted">'.($data->user_info->email).'</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>REQUEST DETAILS</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-3 pt-0">
                                <p class="clearfix">
                                    <span class="float-left">Issued Date:</span>
                                    <span class="float-right text-muted">'.($data->issued_date ?? null).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Return Date:</span>
                                    <span class="float-right text-muted">'.($data->return_date ?? null).'</span>
                                </p>
                                <p class="clearfix">
                                    <div class="d-flex justify-content-between">
                                        <div class="float-left">Overdue Fine:</div>
                                        '.($isRequested && $hasIssue ? 
                                            "<div>
                                                <div class='input-group mb-2'>
                                                    <input type='number' name='request_fine' class='form-control' data-original='{$data->fine}' value='{$data->fine}' style='max-width:90px' min='0'>
                                                    <div class='input-group-append'>
                                                        <div class='input-group-text p-0'>
                                                            &nbsp;<button onclick=\"return save_Request_Fine('{$item_id}');\" id='save_fine_' class='btn hidden btn-outline-success'><i class='fa fa-save'></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>" : "<div>{$data->fine}</div>"
                                        ).'
                                    </div>
                                </p>
                                '.( 
                                    $data->state == "Overdue" ? '
                                        <p class="clearfix">
                                            <span class="float-left">Fine Paid:</span>
                                            <span class="float-right text-muted">'.($data->actual_paid ?? null).'</span>
                                        </p>
                                    ' : ''
                                ).'
                                <p class="clearfix">
                                    <span class="float-left">Current State:</span>
                                    <span class="float-right text-muted">'.$myClass->the_status_label($data->state).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Date:</span>
                                    <span class="float-right text-muted">'.($data->updated_at ?? null).'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="padding-20">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="books_list-tab2" data-toggle="tab" href="#books_list" role="tab" aria-selected="true">Books List</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="comments-tab2" data-toggle="tab" href="#comments" role="tab" aria-selected="true">Comments</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="books_list" role="tabpanel" aria-labelledby="books_list-tab2">
                                    <div class="d-flex justify-content-between">
                                        <div><h5 class="text-uppercase">Books Selected List</h5></div>
                                        '.($isEditable ? 
                                            ($hasIssue && $isRequested ? 
                                                "<div><button onclick='return approve_Cancel_Books_Request(\"{$item_id}\",\"approve_request\");' class='btn btn-outline-success'><i class='fa fa-save'></i> Approve Request</button></div>" : 
                                                (
                                                    !$hasIssue && $isRequested ? 
                                                        "<div><button onclick='return approve_Cancel_Books_Request(\"{$item_id}\",\"cancel_request\");' class='btn btn-outline-danger'><i class='fa fa-times'></i> Cancel Request</button></div>" : 
                                                        (
                                                            $hasIssue ? "<div><button onclick='return return_Requested_Book(\"entire_order\",\"{$item_id}\",\"{$data->fine}\");' class='btn btn-outline-success'><i class='fa fa-reply'></i> Return All</button></div>": ""
                                                        )
                                                )
                                            ) : ''
                                        ).'
                                    </div>
                                    <div class="mt-3">
                                        '.$books_listing.'
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab2">
                                    <div class="col-lg-12 pl-0"><h5>Readers\'s Comments</h5></div>
                                    <div>
                                        '.($hasIssue ? leave_comments_builder("books_request", $item_id, false) : "").'
                                        <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$item_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                        <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary">Loading comments</button></div>    
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

    }


}
// print out the response
echo json_encode($response);