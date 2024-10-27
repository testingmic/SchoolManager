<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$userId = $session->userId;
$clientId = $session->clientId;

// specify some variables
$limit = 2000;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

// get the filter values
$filter = (object) array_map("xss_clean", $_POST);

$pageTitle = "User Activity Timelines";
$response->title = $pageTitle;

// if the user has no permissions
if(!$accessObject->hasAccess("manage", "settings")) {
    // show the error page
    $response->html = page_not_found("permission_denied");
} else {

    // include the script to be executed on the page.
    $response->scripts = ["assets/js/timeline.js"];

    // set the where clause to use
    $whereClause = "1";
    $whereClause .= !empty($filter->clientId) ? " AND a.client_id='{$filter->clientId}'" : "";
    $whereClause .= !empty($filter->user_id) ? " AND a.user_id='{$filter->user_id}'" : "";
    $whereClause .= !empty($filter->gender) ? " AND a.gender='{$filter->gender}'" : "";
    $whereClause .= !empty($filter->status) ? " AND a.user_status='{$filter->status}'" : "";
    $whereClause .= !empty($filter->user_role) ? " AND a.user_type='".strtolower($filter->user_role)."'" : "";

    // set the gender and user role list
    $gender_list = ["Male", "Female"];
    $user_role_list = $myClass->all_user_roles_list;
    $status_list = array_unique(array_merge($myClass->default_statuses_list, $myClass->staff_statuses));

    // get the list of schools
    $load_schools_list = $isSupport ? $myClass->pushQuery("*", "clients_accounts") : [];

    // get the array list of values
    $users_array_list = $myClass->pushQuery("a.id, a.client_id, a.item_id, a.name, a.gender, a.user_type, a.user_status, 
            a.access_level, a.date_of_birth, a.unique_id, b.client_name, a.email", 
        "users a LEFT JOIN clients_accounts b ON b.client_id = a.client_id",
        "{$whereClause} ORDER BY a.firstname LIMIT {$limit}");

    $users_list = "";
    $counter = 0;
    // loop through the results list
    foreach($users_array_list as $user) {
    
        $counter++;

        $action = "&nbsp;<button title='Reset User Password' onclick=\"return modal_popup('reset_password_mod', '{$user->unique_id}', 'Reset Password - {$user->name}', {$counter})\" 
            class=\"btn btn-sm mb-1 btn-outline-warning\"><i class=\"fa fa-lock\"></i></button>";

        if(($user->item_id !== $userId) && ($user->user_status !== 'Deleted')) {
            $action .= "&nbsp;<span title='Delete User Record' onclick='return delete_record(\"{$user->item_id}\", \"user\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></span>";
        }

        $users_list .= "<tr data-row_id=\"{$user->item_id}\">";
        $users_list .= "<td>{$counter}</td>";
        $users_list .= "<td>
                ".strtoupper($user->name)."
                <div><span class='badge badge-primary'>{$user->email}</span></div>
            </td>";
        $users_list .= "<td>".ucwords($user->user_type)."</td>";
        $users_list .= "<td>{$user->gender}</td>";
        $users_list .= "<td>{$user->date_of_birth}</td>";
        $users_list .= "<td>".character_limiter($user->client_name, 30)."</td>";
        $users_list .= "<td class='text-center'>".$myClass->the_status_label($user->user_status)."</td>";
        $users_list .= "<td align='center'>{$action}</td>";
        $users_list .= "</tr>";

    }
    
    // set the array stream
    $response->array_stream["users_list_array"] = $users_array_list;

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
                <div class="col-lg-3 col-md-3">
                    <label>Academic Institution</label>
                    <select data-width="100%" class="form-control selectpicker" name="clientId">
                        <option value="">All Academic Institutions</option>';
                        foreach($load_schools_list as $school) {
                            $selected = !empty($filter->clientId) && ($school->client_id == $filter->clientId) ? 'selected' : '';
                            $response->html .= '<option value="'.$school->client_id.'" '.$selected.'>'.$school->client_name.'</option>';
                        }
                    $response->html .= '
                    </select>
                </div>
                <div class="col-lg-2 col-md-2">
                    <label>User Status</label>
                    <select data-width="100%" class="form-control selectpicker" name="status">
                        <option value="">All User Status</option>';
                        foreach($status_list as $status) {
                            $selected = !empty($filter->status) && ($status == $filter->status) ? 'selected' : '';
                            $response->html .= '<option value="'.$status.'" '.$selected.'>'.$status.'</option>';
                        }
                    $response->html .= '
                    </select>
                </div>
                <div class="col-lg-3 col-md-3">
                    <label>User Role</label>
                    <select data-width="100%" class="form-control selectpicker" name="user_role">
                        <option value="">All User Role</option>';
                        foreach($user_role_list as $role) {
                            $selected = !empty($filter->user_role) && ($role == $filter->user_role) ? 'selected' : '';
                            $response->html .= '<option value="'.$role.'" '.$selected.'>'.$role.'</option>';
                        }
                    $response->html .= '
                    </select>
                </div>
                <div class="col-lg-2 col-md-2">
                    <label>User Gender</label>
                    <select data-width="100%" class="form-control selectpicker" name="gender">
                        <option value="">All User Gender</option>';
                        foreach($gender_list as $gender) {
                            $selected = !empty($filter->gender) && ($gender == $filter->gender) ? 'selected' : '';
                            $response->html .= '<option value="'.$gender.'" '.$selected.'>'.$gender.'</option>';
                        }
                    $response->html .= '
                    </select>
                </div>
                <div class="col-lg-2 col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button id="filter_Users_List" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-lg-12 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive table-student_staff_list">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>User Name</th>
                                        <th>User Role</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                        <th>Academic Institution</th>
                                        <th>Status</th>
                                        <th width="8%" align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$users_list.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

    // append the reset password modal
    $response->html .= reset_password_modal();
}
// print out the response
echo json_encode($response);
?>