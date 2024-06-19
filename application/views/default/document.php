<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Document Info";
$response->title = $pageTitle;

// set the item unique id
$unique_id = confirm_url_id(1) ? $SITEURL[1] : null;

// set the query object parameter
$param = (object)[
    "unique_id" => $unique_id ?? null,
    "list_tree" => (bool) $unique_id,
    "clientId" => $clientId,
    "limit" => 1,
    "state" => ["Trash", "Active"]
];

// confirm that the school has the documents manager feature enabled
if(!in_array("documents_manager", $clientFeatures) || empty($unique_id)) {

    // permission denied
    $response->html = page_not_found("not_found");

} else {

    // init param
    $document = [];

    // load the document information
    $documents_array = load_class("documents", "controllers")->list($param)["data"];

    // if the directory list is not empty
    if(isset($documents_array["directory_list"])) {
        // get the document details
        $document = $documents_array["directory_list"];
    }

    // if the files list is not empty
    elseif(isset($documents_array["file_list"])) {
        // get the document details
        $document = $documents_array["file_list"];
    }

    // if no record was found
    if(!$document) {
        // permission denied
        $response->html = page_not_found("not_found");
        // print out the response
        echo json_encode($response);
        exit;
    }

    // get the document summary
    $document_summary = document_summary($document);

    // get the first key of the do found
    $document = $document[0];
    
    // get the file to display
    $document_file = !empty($document->attachment) ? 
        document_file($document->attachment, $document->file_ref_id) : [];

    // print_r($document_file);

    // set the document details
    $document_details = "
        <div class=\"mb-3\">
            <label class=\"pb-0 text-primary mb-0\">".ucfirst($document->type)." Name:</label>
            <div class=\"font-20\">{$document->name}</div>
        </div>
        ".(!empty($document->description) ?
            "<div class=\"mb-3\">
                <label class=\"pb-0 text-primary mb-0\">Description:</label>
                <div class=\"font-15\">{$document->description}</div>
            </div>" : null
        )."
        ".($document->type === "file" ? 
            "<div class=\"row\">
                <div class=\"col-md-6 mb-3\">
                    <label class=\"pb-0 text-primary mb-0\">File Size:</label>
                    <div class=\"font-15\">{$document->file_size}</div>
                </div>
                <div class=\"col-md-6 mb-3\">
                    <label class=\"pb-0 text-primary mb-0\">Created By:</label>
                    <div class=\"font-15\">{$document->fullname}</div>
                </div>
                <div class=\"col-md-6 mb-3\">
                    <label class=\"pb-0 text-primary mb-0\">Document Type:</label>
                    <div class=\"font-15\">{$document->file_type}</div>
                </div>
                <div class=\"col-md-6 mb-3\">
                    <label class=\"pb-0 text-primary mb-0\">Downloads Count:</label>
                    <div class=\"font-15\">{$document->downloads_count}</div>
                </div>
            </div>" : "
            <div class=\"mb-3\">
                <label class=\"pb-0 text-primary mb-0\">Created By:</label>
                <div class=\"font-15\">{$document->fullname}</div>
            </div>"
        )."
        <div class=\"row\">
            <div class=\"col-md-6 mb-3\">
                <label class=\"pb-0 text-primary mb-0\">Date Created:</label>
                <div class=\"font-15\">{$document->date_created}</div>
            </div>
            <div class=\"col-md-6 mb-3\">
                <label class=\"pb-0 text-primary mb-0\">Last Updated:</label>
                <div class=\"font-15\">{$document->last_updated}</div>
            </div>
        </div>
        ".($document->type === "directory" ? 
            "
            <div class='text-success font-15 font-bold mb-2 border-bottom'>Document Summary</div>
            <div>
                <div class='mb-1'><span class='font-bold'>Files Count: </span><span data-summary='files_count' class='float-right'>{$document_summary["summary"]["files_count"]}</span></div>
                <div class='mb-1'><span class='font-bold'>Folders Count: </span><span data-summary='folders_count' class='float-right'>{$document_summary["summary"]["folder_count"]}</span></div>
                <div class='mb-1'><span class='font-bold'>Last Updated: </span><span data-summary='last_updated' class='float-right'>{$document_summary["summary"]["last_updated"]}</span></div>
            </div> " : null)."
    ";

    // uploads script
    $response->scripts = ["assets/js/comments.js", "assets/js/documents.js"];

    // document information
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'documents">Document Manager</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-5">
                    '.(!empty($document_file) && in_array($document_file["type"], ["jpg", "jpeg", "png", "gif", ".webp"]) ? 
                        '<div class="rs-gallery-4 rs-gallery">
                            <div class="text-center mb-2">
                                <div class="gallery-item">
                                    <div class="col-lg-12 attachment-item border" style="padding:10px">
                                        <div style="height:250px" title="" data-toggle="tooltip" data-original-title="Click to preview"><img height="100%" width="100%" src="'.$baseUrl.''.$document_file["path"].'">
                                        </div>
                                        <div class="gallery-desc">
                                            <a class="image-popup anchor" href="'.$baseUrl.''.$document_file["path"].'" title="'.$document_file["name"].' ('.$document_file["size"].') on '.$document_file["datetime"].'">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>' : null
                    ).'
                    <div class="card">
                        <div class="card-body">
                            '.$document_details.'
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <div class="slim-scroll">
                                <div class="p-0 m-0">
                                    '.leave_comments_builder("document", $unique_id, false).'
                                    <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$unique_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                    <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

}

// print out the response
echo json_encode($response);
?>