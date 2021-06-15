<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$search_term = isset($_GET["term"]) ? xss_clean($_GET["term"]) : null;

$pageTitle = "Fee Payment Log Search";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/payments.js"];

$the_form = "";

// load fees allocation list for the students
$fees_category_list = "";
$stmt = $myClass->db->prepare("
    SELECT a.*, 
        (SELECT COUNT(*) FROM fees_allocations b WHERE a.id = b.category_id AND b.client_id = a.client_id) AS fees_count
    FROM fees_category a
    WHERE client_id = ? AND a.status = ? ORDER BY a.id
");
$stmt->execute([$clientId, 1]);
$fees_category_array = $stmt->fetchAll(PDO::FETCH_OBJ);

// fees category
foreach($fees_category_array as $category) {
    $fees_category_list .= "<option value=\"{$category->id}\">{$category->name}</option>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Fee Payment List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row" id="finance_search_field">
            <div class="col-12 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body"">
                        <div class="form-group">
                            <label>Enter the Student Name or Receipt ID</label>
                            <input value="'.$search_term.'" type="text" placeholder="Search by Student Name or Receipt ID" name="log_search_term" id="log_search_term" class="form-control">
                        </div>
                        <div align="center" class="form-group mb-2">
                            <button class="btn btn-outline-primary" onclick="return search_Payment_Log()"><i class="fa fa-filter"></i> Search</button>
                        </div>
                        <div class="mt-0 mb-2 border-bottom"></div>
                        <div id="log_search_term_list" class="slim-scroll custom-600px"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <label>Filter by Category</label>
                                <select data-width="100%" id="category_id" class="selectpicker form-control">
                                    <option value="">Select Category</option>
                                    '.$fees_category_list.'
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Start Date</label>                                
                                <input value="'.date("Y-m-d", strtotime("first day this month")).'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                            </div>
                            <div class="col-lg-3">
                                <label>End Date</label>
                                <input value="'.date("Y-m-t", strtotime("last day this month")).'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                            </div>
                            <div class="col-lg-2">
                                <label>&nbsp;<br></label>
                                <button type="button" onclick="return generate_payment_report();" class="btn btn-block btn-primary"><i class="fa fa-adjust"></i> Generate</button>
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