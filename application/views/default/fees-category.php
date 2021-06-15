<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$response->title = "Fees Category List : {$appName}";
$response->scripts = ["assets/js/fees.js"];

$category_param = (object) [
    "client_data" => $defaultUser->client,
    "clientId" => $session->clientId
];
$fees_category_array_list = load_class("fees", "controllers", $category_param)->category_list($category_param);

$hasAdd = $accessObject->hasAccess("add", "fees_category");
$hasUpdate = $accessObject->hasAccess("update", "fees_category");

$sections = "";
$category_array_list = [];
foreach($fees_category_array_list["data"] as $key => $each) {
    
    $category_array_list[$each->id] = $each;
    
    $action = "";
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to update this {$each->name} category' href='#' onclick='return update_fees_category(\"{$each->id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
        $action .= "&nbsp;<a title='Click to delete this {$each->name} category' href='#' onclick='return delete_record(\"{$each->id}\", \"fees_category\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $sections .= "<tr data-row_id=\"{$each->id}\">";
    $sections .= "<td>".($key+1)."</td>";
    $sections .= "<td>{$each->name}</td>";
    $sections .= "<td>{$each->code}</td>";
    $sections .= "<td>{$each->amount}</td>";
    $sections .= "<td>{$each->fees_count}</td>";
    $sections .= "<td align='center'>{$action}</td>";
    $sections .= "</tr>";
}

// append the questions list to the array to be returned
$response->array_stream["fees_category_array_list"] = $category_array_list;


$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Fees Category List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Fees Category List</div>
            </div>
        </div>
        <div data-backdrop="static" data-keyboard="false" class="modal fade" id="feesCategoryModal">
            <form action="'.$baseUrl.'api/fees/savecategory" class="ajax-data-form" id="ajax-data-form-content">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Allowance / Deduction Types</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" maxlength="100" placeholder="Type name" name="name" id="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="code">Code</label>
                                        <input type="text" maxlength="12" placeholder="Category Code" name="code" id="code" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="default_amount">Default Amount</label>
                                        <input type="number" min="1" maxlength="20" placeholder="Type default amount" name="amount" id="amount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea placeholder="" maxlength="255" name="description" id="description" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer p-0">
                                <input type="hidden" name="category_id">
                                <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                                <button type="button-submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-sm btn-outline-primary"  onclick="return add_fees_category();" href="#"><i class="fa fa-plus"></i> Add Category</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Amount</th>
                                        <th>Fees Count</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$sections.'</tbody>
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