<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $clientFeatures, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Library Book Details";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!in_array("library", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

// item id
$item_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "book_id" => $item_id,
        "limit" => 1
    ];

    $response->scripts = [
        "assets/js/comments.js",
        "assets/js/library.js",
        "assets/js/upload.js"
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

        // create a new object of the forms class
        $formsObj = load_class("forms", "controllers");

        // guardian information
        $the_form = $formsObj->library_book_form($item_param);
        $hasUpdate = $accessObject->hasAccess("update", "library");

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;
        
        /** Set the module */
        $module = "ebook_{$item_id}";

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "module" => $module,
            "userData" => $defaultUser,
            "item_id" => $item_id,
            "ismultiple" => true,
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf", ".flv", ".mpg", ".mpeg"]),
        ];

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-book-open"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'books">Books List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                    <div class="card-body pr-1 pl-1">
                        <div class="author-box-center">
                            '.(!empty($data->book_image) ? 
                            '<div class="text-center flex items-center justify-center">
                                <img alt="image" src="'.$baseUrl.''.$data->book_image.'" class="item-picture">
                            </div>' : '').'
                            <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->title.'</div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Description</h4>
                    </div>
                    <div class="card-body">
                        <div class="pt-0">
                            '.($data->description ? $data->description : "-").'
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Book Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Collection</span>
                                <span class="float-right text-muted">'.($data->category_name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Class</span>
                                <span class="float-right text-muted">'.($data->class_name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Department</span>
                                <span class="float-right text-muted">'.($data->department_name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Price</span>
                                <span class="float-right text-muted">'.($defaultCurrency ?? null).' '.number_format($data->price ?? 0, 2).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Code</span>
                                <span class="float-right text-muted">'.($data->code ?? null).'</span>
                            </p>
                            <div class="clearfix flex">
                                <div class="w-1/2">Author</div>
                                <div class="w-1/2 text-right text-muted">'.($data->author ?? null).'</div>
                            </div>
                            <p class="clearfix">
                                <span class="float-left">ISBN</span>
                                <span class="float-right text-muted">'.($data->isbn ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Stock Quantity</span>
                                <span class="float-right text-muted">'.($data->books_stock ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Publish Date</span>
                                <span class="float-right text-muted">'.($data->publish_date ?? null).'</span>
                            </p>
                            <div class="clearfix flex">
                                <div class="w-1/2">Publisher</div>
                                <div class="w-1/2 text-right text-muted">'.($data->publisher ?? null).'</div>
                            </div>
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
                <div class="card stick_to_top">
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
                            <div class="d-flex justify-content-between">
                                <div><h5>E-Resources List</h5></div>
                                '.($hasUpdate ? "<div><button onclick='return show_EResource_Modal();' class='btn btn-outline-primary btn-sm'><i class='fa fa-plus'></i> Upload</button></div>" : null).'
                            </div>
                            '.(!empty($data->attachment_html) ? "<div data-ebook_resource_list='{$item_id}'>{$data->attachment_html}</div>" : "<div class='font-italic'>No E-Resources have been uploaded under this book.</div>").'
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
                                '.leave_comments_builder("ebook", $item_id, false).'
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
        </section>
        <div class="modal fade" id="ebook_Resource_Modal_Content" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" style="width:100%;height:100%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><div><strong>Upload E-Resource</strong></div></h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-2">
                            <div class="p-3">
                                '.$formsObj->form_attachment_placeholder($form_params).'
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button data-dismiss="modal" class="btn btn-outline-secondary">CLose</button>
                                    </div>
                                    <div>
                                        <button onclick="return upload_EBook_Resource(\''.$item_id.'\');" class="btn btn-outline-success btn-sm"><i class="fa fa-upload"></i> Upload Files</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

}
// print out the response
echo json_encode($response);
?>