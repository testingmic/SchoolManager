<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Library Book Details";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

$response->scripts = [
    "assets/js/comments.js",
    "assets/js/comments_upload.js"
];

// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "book_id" => $item_id,
        "limit" => 1
    ];

    $data = load_class("library", "controllers")->list($item_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];
        $item_param = $data;
        $item_param->clientId = $clientId;

        // guardian information
        $the_form = load_class("forms", "controllers")->library_book_form($item_param);
        $hasUpdate = $accessObject->hasAccess("update", "library");

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-books">Books List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <div class="clearfix"></div>
                        <div class="author-box-name"><a href="#">'.$data->title.'</a></div>
                        <div class="author-box-job">('.$data->category_name.')</div>
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Description</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="py-3 pt-0">
                            '.$data->description.'
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Book Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Class</span>
                                <span class="float-right text-muted">'.($data->class_name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Department</span>
                                <span class="float-right text-muted">'.($data->department_name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Code</span>
                                <span class="float-right text-muted">'.($data->code ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Author</span>
                                <span class="float-right text-muted">'.($data->author ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">ISBN</span>
                                <span class="float-right text-muted">'.($data->isbn ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Stock Quantity</span>
                                <span class="float-right text-muted">'.($data->quantity ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Rack Number</span>
                                <span class="float-right text-muted">'.($data->rack_no ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Row Number</span>
                                <span class="float-right text-muted">'.($data->row_no ?? null).'</span>
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
                            <a class="nav-link '.(!$updateItem ? "active" : null).'" id="resources-tab2" data-toggle="tab" href="#resources" role="tab" aria-selected="true">E-Books</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="comments-tab2" data-toggle="tab" href="#comments" role="tab" aria-selected="true">Comments</a>
                        </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="resources" role="tabpanel" aria-labelledby="resources-tab2">
                            <div class="col-lg-12 pl-0"><h5>E-Resources List</h5></div>
                            '.(!empty($data->attachment_html) ? $data->attachment_html : "<div class='font-italic'>No E-Resources have been uploaded under this book.</div>").'
                        </div>
                        <div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        // if the user has the permissions to update the book details
                        if($hasUpdate) {
                            $response->html .= $the_form;
                        }

                        $response->html .= '
                        </div>
                        <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab2">
                            <div class="col-lg-12 pl-0"><h5>Readers\'s Comments</h5></div>
                            <div>
                                '.leave_comments_builder("book", $item_id, false).'
                                <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$item_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary">Loading comments</button></div>    
                            </div>                            
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
?>