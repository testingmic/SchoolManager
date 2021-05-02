<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $SITEURL;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Fees Allocation";
$response->title = "{$pageTitle} : {$appName}";

$receivePayment = $accessObject->hasAccess("receive", "fees");

/** confirm that the user has the permission to receive payment */
if(!$receivePayment) {
    $response->html = page_not_found();
} else {

    // the allocation fees
    $canAllocate = $accessObject->hasAccess("allocation", "fees");

    // scripts for the page
    $response->scripts = ["assets/js/filters.js", "assets/js/payments.js"];

    // load the classes list
    $classes_param = (object) ["clientId" => $clientId, "columns" => "id, name"];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // load fees allocation list for class
    $allocation_param = (object) ["clientId" => $clientId, "userData" => $defaultUser, "receivePayment" => $receivePayment, "canAllocate" => $canAllocate];
    $allocation_param->client_data = $defaultUser->client;
    
    // create a new object
    $feesObject = load_class("fees", "controllers", $allocation_param);

    // load the class allocation list
    $class_allocation_list = $feesObject->class_allocation_array($allocation_param);

    // load fees allocation list for the students
    $student_allocation_list = $feesObject->student_allocation_array($allocation_param);

    // info
    $info = "Use this form to assign fees to a class or to a particular student. Leave the student id field blank if you want to set for the entire class.";

    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Fees Payment List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row mt-sm-4" id="filter_Department_Class">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <div style="width:100%" class="d-flex justify-content-between">
                                <div><h4>Allocate Fees To Class/Student</h4></div>
                                <div class="text-right"><i data-toggle="tooltip" title="'.$info.'" class="fa cursor text-primary fa-info"></i></div>
                            </div>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-3 pt-0" id="fees_allocation_form">
                            
                                <div class="form-group">
                                    <label>Allocate To <span class="required">*</span></label>
                                    <select class="form-control selectpicker" id="allocate_to" name="allocate_to">
                                        <option value="class">Entire Class</option>
                                        <option value="student">Specific Student</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Class <span class="required">*</span></label>
                                    <select class="form-control selectpicker" name="class_id">
                                        <option value="">Please Select Class</option>';
                                        foreach($class_list as $each) {
                                            $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group hidden" id="students_list">
                                    <label>Select Student <span class="required">*</span></label>
                                    <select data-width="100%" class="form-control selectpicker" name="student_id">
                                        <option value="">Please Select Student</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Category <span class="required">*</span></label>
                                    <select class="form-control selectpicker" name="category_id">
                                        <option value="">Please Select Category</option>';
                                        foreach($myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                                    $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Set Amount <span class="required">*</span></label>
                                    <input type="number" name="amount" id="amount" class="form-control">
                                </div>

                                <div class="form-group text-right mb-0" id="allocate_fees_button">
                                    <button onclick="return save_Fees_Allocation()" class="btn btn-outline-success"><i class="fa fa-save"></i> Allocate Fee</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="classes-tab2" data-toggle="tab" href="#classes" role="tab" aria-selected="true">Class Allocation</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="false">Student Allocation</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="classes" role="tabpanel" aria-labelledby="classes-tab2">
                                    <div class="table-responsive">
                                        <table data-empty="" class="table table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">#</th>
                                                    <th>Class</th>
                                                    <th>Fees Category</th>
                                                    <th>Amount</th>
                                                    <th align="center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$class_allocation_list.'</tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab2">
                                    <div class="table-responsive">
                                        <table data-empty="" class="table table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">#</th>
                                                    <th>Student Name</th>
                                                    <th>Category</th>
                                                    <th>Due</th>
                                                    <th>Paid</th>
                                                    <th align="center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$student_allocation_list.'</tbody>
                                        </table>
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

// print out the response
echo json_encode($response);
?>