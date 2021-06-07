<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Email Templates";
$response->title = "{$pageTitle} : {$appName}";

// add the scripts to load
$response->scripts = ["assets/js/communication.js"];

// set the parameters
$params = (object) [
    "route" => "email",
    "type" => "email",
    "clientId" => $clientId
];

// get the list of all the templates
$templates_array = load_class("communication", "controllers")->list_templates($params)["data"];

// confirm that the user has the required permissions
$the_form = load_class("forms", "controllers")->smsemail_template_form($params);

// init variables
$count = 0;
$list_templates = "";
$templates_array_list = [];

// loop through the templates list
foreach($templates_array as $key => $template) {
    $templates_array_list[$template->item_id] = $template;
    $count++;

    // view button
    $checkbox = "";
    $action = "<button onclick='return view_template(\"{$template->item_id}\", \"api/communication/update_template\");' title='Click to view full details of template' class='btn btn-outline-success mb-1 btn-sm'><i class='fa fa-eye'></i></button>";

    // if the record is still pending
    $action .= "&nbsp;<button onclick='return delete_record(\"{$template->item_id}\", \"template\");' title='Click to reverse this template' class='btn btn-outline-danger mb-1 btn-sm'><i class='fa fa-trash'></i></button>";

    $list_templates .= "<tr data-row_id=\"{$template->item_id}\">";
    $list_templates .= "<td>{$count}</td>";
    $list_templates .= "<td>{$template->name}</td>";
    $list_templates .= "<td>{$template->message}</td>";
    $list_templates .= "<td>".date("jS M Y", strtotime($template->date_created))."</td>";
    $list_templates .= "<td align='center'>{$action}</td>";
    $list_templates .= "</tr>";
}

$response->array_stream["templates_array_list"] = $templates_array_list;

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="padding-20">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="templates_list-tab2" data-toggle="tab" href="#templates_list" role="tab" aria-selected="true"><i class="fa fa-list"></i> Templates List</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="add_template-tab2" data-toggle="tab" href="#add_template" role="tab" aria-selected="true"><i class="fa fa-edit"></i> Add Template</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="templates_list" role="tabpanel" aria-labelledby="templates_list-tab2">
                                    <div class="table-responsive trix-slim-scroll">
                                        <table class="table table-bordered table-striped datatable">
                                            <thead>
                                                <th></th>
                                                <th>Name</th>
                                                <th width="50%">Body</th>
                                                <th>Date</th>
                                                <th></th>
                                            </thead>
                                            <tbody>'.$list_templates.'</tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="add_template" role="tabpanel" aria-labelledby="add_template-tab2">
                                    '.$the_form.'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
    
// print out the response
echo json_encode($response);
?>