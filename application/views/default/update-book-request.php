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
        $response->scripts = ["assets/js/library.js"];

        // list the books in this request
        $books_list = "";

        // set the permission 
        $isPermitted = (bool) ($hasIssue && $data->state === "Requested");
        $isRequested = (bool) ($data->state === "Requested");
        $isOverdue = (bool) ($data->state === "Overdue");

        // loop through the books list
        foreach($data->books_list as $book) {
            // set the permission
            $books_list .= "<tr class='each_book_item' data-request_id='{$item_id}' data-book_id='{$book->book_id}'>";
            $books_list .= "<td>
                <div class='d-flex justify-content-start'>
                    <div class='mr-2'>".(!empty($book->book_image) ? "<img src='{$baseUrl}{$book->book_image}' width='50px' height='40px'>" : "")."</div>
                    <div><a href='{$baseUrl}update-book/{$book->book_id}'>{$book->title}</a> <br> <strong>{$book->isbn}</strong></div>
                </div>
            </td>";
            $books_list .= "<td>{$book->author}</td>";

            // if the user has the required permissions
            if($isPermitted) {
                $books_list .= "<td><input type='number' min='1' max='{$book->books_stock}' class='form-control' style='width:100px' data-request_id='{$item_id}' data-book_id='{$book->book_id}' data-original='{$book->quantity}' value='{$book->quantity}'></td>";
            } else {
                $books_list .= "<td>{$book->quantity}</td>";
            }

            $books_list .= "<td align='center'>";

            // if the item is not yet overdue
            if($isPermitted && !$isOverdue) {
                if($isRequested) {
                    $books_list .= "<button onclick=\"return remove_Book('{$item_id}','{$book->book_id}');\" class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></button>";
                    $books_list .= "&nbsp;<button onclick=\"return save_Book('{$item_id}','{$book->book_id}');\" id='save_book_{$book->book_id}' class='btn btn-sm hidden btn-outline-success'><i class='fa fa-save'></i></button>";
                }
            }
            $books_list .= "</td>";

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
                        <th width="13%"></th>
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
                                    <span class="float-left">Overdue Fine:</span>
                                    <span class="float-right text-muted">'.($data->fine ?? null).'</span>
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
                                        <div><h4 class="text-uppercase">Books Selected List</h4></div>
                                        '.($hasIssue ? "<div><button onclick='return show_EResource_Modal();' class='btn btn-outline-primary btn-sm'><i class='fa fa-plus'></i> Upload</button></div>" : null).'
                                    </div>
                                    <div class="mt-3">
                                        '.$books_listing.'
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab2">
                                    <div class="col-lg-12 pl-0"><h5>Readers\'s Comments</h5></div>
                                    
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