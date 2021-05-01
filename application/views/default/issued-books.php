<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];

$hasIssue = $accessObject->hasAccess("issue", "library");
$pageTitle = $hasIssue ? "Issued Books List" : "My Books List";

$response->title = "{$pageTitle} : {$appName}";

// begin the request parameter
$params = (object) ["clientId" => $session->clientId, "show_list" => true, "limit" => 99999, "userData" => $defaultUser];
$item_list = load_class("library", "controllers")->issued_request_list($params);

$books_list = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='Click to view details of this request' href='{$baseUrl}update-book-request/{$each->item_id}' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasIssue && in_array($each->status, ["Issued", "Requested"]) && ($each->state !== "Overdue")) {
        $action .= "&nbsp;<a title='Click to delete this issued book record' href='#' onclick='return delete_record(\"{$each->item_id}\", \"borrow\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-stop'></i></a>";
    }

    if(!$hasIssue && ($each->the_type == "request") && in_array($each->status, ["Requested"])) {
        $action .= "&nbsp;<a title='Click to delete this requested book record' href='#' onclick='return delete_record(\"{$each->item_id}\", \"borrow\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-stop'></i></a>";
    }

    $books_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $books_list .= "<td>".($key+1)."</td>";

    // if the user has issue permission
    if($hasIssue) {
        // if the books list is parsed
        $books_list .= "<td>
            {$each->user_info->name}
            <span class='badge badge-primary p-1'>{$each->user_role}</span><br>
            <strong>{$each->user_info->unique_id}</strong>
        </td>";
    }

    $books_ = "";
    foreach($each->books_list as $key => $book) {
        $books_ .= "
        <div class='mb-1'>
            ".($key+1).". {$book->title}
        </div>";
    }
    $books_list .= "<td>{$books_}</td>";
    $books_list .= "<td>{$each->issued_date}</td>";
    $books_list .= "<td>{$each->return_date}</td>";
    $books_list .= "<td>".($each->fine ?? null)."</td>";
    $books_list .= "<td>".$myClass->the_status_label($each->state)."</td>";
    $books_list .= "<td align='center'>{$action}</td>";
    $books_list .= "</tr>";
}
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                '.($hasIssue ? '<a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'issue-book"><i class="fa fa-arrow-circle-right"></i> Issue Book</a>' 
                    : '<a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'request-book"><i class="fa fa-american-sign-language-interpreting"></i> Request Book</a>'
                ).'
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        '.($hasIssue ? '<th>Fullname</th>' : '').'
                                        <th>Books List</th>
                                        '.($hasIssue ? '<th>Date of Issue</th>' : '<th>Date of Request</th>').'
                                        <th>Date of Expiry</th>
                                        <th width="10%">Fine</th>
                                        <th width="10%">Status</th>
                                        <th align="center" width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$books_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>