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
if(!isset($_SERVER["HTTP_REFERER"])) {
    header("location: {$baseUrl}main");
    exit;
}

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Add Student";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/page/index.js"
];

$student_param = (object) [
    "clientId" => $clientId,
    "user_type" => "student"
];

$student_list = load_class("users", "controllers")->list($student_param);

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-student">Students List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                    <form class="ajaxform" action="'.$baseUrl.'api/users/add" method="POST">
                        <div class="row mb-4 border-bottom pb-3">
                            <div class="col-lg-12">
                                <h5>BIO INFORMATION</h5>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="unique_id">Student ID (optional)</label>
                                    <input type="text" name="unique_id" id="unique_id" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="enrollment_date">Enrollment Date <span class="required">*</span></label>
                                    <input type="date" name="enrollment_date" id="enrollment_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="form-control selectpicker">
                                        <option value="null">Select Gender</option>';
                                        foreach($myClass->pushQuery("*", "users_gender") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                $response->html .= '</select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="firstname">Firstname <span class="required">*</span></label>
                                    <input type="text" name="firstname" id="firstname" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Lastname <span class="required">*</span></label>
                                    <input type="text" name="lastname" id="lastname" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="othername">Othernames</label>
                                    <input type="text" name="othername" id="othername" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="phone">Primary Contact</label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="phone_2">Secondary Contact</label>
                                    <input type="text" name="phone_2" id="phone_2" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <select name="country" id="country" class="form-control selectpicker">
                                        <option value="null">Select Country</option>';
                                        foreach($myClass->pushQuery("*", "country") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->country_name}</option>";                            
                                        }
                $response->html .= '</select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="residence">Place of Residence <span class="required">*</span></label>
                                    <input type="text" name="residence" id="residence" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="blood_group">Blood Broup</label>
                                    <select name="blood_group" id="blood_group" class="form-control selectpicker">
                                        <option value="null">Select Blood Group</option>';
                                        foreach($myClass->pushQuery("id, name", "blood_groups") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                $response->html .= '</select>
                                    <input type="hidden" id="user_type" name="user_type" value="student">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 border-bottom pb-4">
                            <div class="col-lg-12">
                                <h5>GUARDIAN INFORMATION</h5>
                            </div>
                            <div class="col-lg-12" id="student_guardian_list">
                                <div class="row" data-row="1">
                                    <div class="col-lg-4 col-md-4">
                                        <label for="guardian_info[guardian_fullname][1]">Fullname</label>
                                        <input type="text" name="guardian_info[guardian_fullname][1]" id="guardian_info[guardian_fullname][1]" class="form-control">
                                        <div class="col-lg-12 col-md-12 pl-0 mt-2">
                                            <label for="guardian_info[guardian_relation][1]">Relationship</label>
                                            <select name="guardian_info[guardian_relation][1]" id="guardian_info[guardian_relation][1]" class="form-control selectpicker">
                                                <option value="null">Select Relation</option>';
                                                foreach($myClass->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                                    $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                                }
                        $response->html .= '</select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 col-md-4">
                                        <label for="guardian_info[guardian_contact][1]">Contact Number</label>
                                        <input type="text" name="guardian_info[guardian_contact][1]" id="guardian_info[guardian_contact][1]" class="form-control">
                                    </div>
                                    <div class="col-lg-3 col-md-3">
                                        <label for="guardian_info[guardian_email][1]">Email Address</label>
                                        <input type="text" name="guardian_info[guardian_email][1]" id="guardian_info[guardian_email][1]" class="form-control">
                                    </div>
                                    <div class="col-lg-1 col-md-1 text-right">
                                        <div class="d-flex justify-content-end">
                                            <div class="mr-1">
                                                <br>
                                                <button data-row="1" class="btn append-row btn-primary" type="button"><i class="fa fa-plus"></i> Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4 border-bottom pb-4">
                            <div class="col-lg-12"><h5>ACADEMICS</h5></div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="class_id">Class</label>
                                    <select name="class_id" id="class_id" class="form-control selectpicker">
                                        <option value="null">Select Student Class</option>';
                                        foreach($myClass->pushQuery("id, name", "classes", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                $response->html .= '</select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="department">Department <span class="required">*</span></label>
                                    <select name="department" id="department" class="form-control selectpicker">
                                        <option value="">Select Student Department</option>';
                                        foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                $response->html .= '</select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="section">Section</label>
                                    <select name="section" id="section" class="form-control selectpicker">
                                        <option value="null">Select Student Section</option>';
                                        foreach($myClass->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                $response->html .= '</select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>