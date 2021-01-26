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
$pageTitle = "Book Category Details";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$response->scripts = [
    "assets/js/library.js"
];

// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

// if the user id is not empty
if(!empty($item_id)) {

    // parameters for the category
    $item_param = (object) [
        "clientId" => $clientId,
        "category_id" => $item_id,
        "limit" => 1
    ];
    $data = load_class("library", "controllers")->category_list($item_param);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];
        $item_param = $data;
        $item_param->clientId = $clientId;

        // parameters for the book list
        $_param = (object) [
            "clientId" => $clientId,
            "category_id" => $data->id,
            "limit" => 99999
        ];
        $books_list = load_class("library", "controllers")->list($_param);
        
        // category books list
        $category_books_list = "";

        // loop through the books list
        foreach($books_list["data"] as $key => $book) {
            
            // view link
            $action = "<a href='{$baseUrl}update-book/{$book->item_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

            $category_books_list .= "<tr data-row_id=\"{$book->item_id}\">";
            $category_books_list .= "<td>".($key+1)."</td>";
            $category_books_list .= "<td><a href='{$baseUrl}update-book/{$book->item_id}'>{$book->title}</a></td>";
            $category_books_list .= "<td>{$book->author}</td>";
            $category_books_list .= "<td>{$book->quantity}</td>";
            $category_books_list .= "<td><span class='underline'>".($book->isbn ?? null)."</span></td>";
            $category_books_list .= "<td align='center'>{$action}</td>";
            $category_books_list .= "</tr>";
        }

        // create a new object of the forms class
        $formsObj = load_class("forms", "controllers");

        // guardian information
        $the_form = $formsObj->library_category_form($item_param);
        $hasUpdate = $accessObject->hasAccess("update", "library");

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;
        
        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-books">Books List</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-books-category">Books Category List</a></div>
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
                        <div class="author-box-name"><a href="#">'.$data->name.'</a></div>
                    </div>
                </div>
                </div>
                '.($data->description ? 
                    '<div class="card">
                        <div class="card-header">
                            <h4>Description</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="py-3 pt-0">
                                '.$data->description.'
                            </div>
                        </div>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-header">
                        <h4>Book Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Department</span>
                                <span class="float-right text-muted">'.($data->department_name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Books Count</span>
                                <span class="float-right text-muted">'.($data->books_count ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Date Created</span>
                                <span class="float-right text-muted">'.($data->date_created ?? null).'</span>
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
                            <a class="nav-link '.(!$updateItem ? "active" : null).'" id="resources-tab2" data-toggle="tab" href="#resources" role="tab" aria-selected="true">Books List</a>
                        </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="resources" role="tabpanel" aria-labelledby="resources-tab2">
                            <div class="col-lg-12">
                                <div class="mb-3 border-bottom"><h5>Category Books List</h5></div>
                                <div class="table-responsive">
                                    <table data-empty="" class="table table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th>Book Title</th>
                                                <th>Author</th>
                                                <th width="15%">Stock</th>
                                                <th>ISBN</th>
                                                <th align="center" width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>'.$category_books_list.'</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
                        // if the user has the permissions to update the book details
                        if($hasUpdate) {
                            $response->html .= $the_form;
                        }

                        $response->html .= '
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