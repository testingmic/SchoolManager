<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $isSupport;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

$response->title = "Knowledge Base ";
$response->scripts = ["assets/js/support.js"];

$knowledge_id = (confirm_url_id(1, "item") && confirm_url_id(2)) ? $SITEURL[2] : null;

// set the parameters
$item_param = (object) [
    "baseUrl" => $baseUrl,
    "clientId" => $clientId,
    "width" => "col-lg-4",
    "knowledge_id" => $knowledge_id,
    "client_data" => $defaultUser->client
];

// if the ticket id is not empty
// then set to load the replies as well
$item_param->show_all = true;

// get the list of all the templates
$support_array = load_class("support", "controllers", $item_param)->knowledgebase_list($item_param)["data"];

// init variables
$count = 0;
$item_found = false;
$isPermitted = false;
$knowledge_base_list = $myClass->tutorials_list($item_param);

// else if the support ticket was parsed and the item is not empty
if($knowledge_id && !empty($support_array)) {
    // get the first array key item
    $data = $support_array[0];

    // set the item found variable to true
    $item_found = true;

    // replace underscores with space
    $data->section = str_ireplace("_", " ", $data->section);
    
    // confirm if the ticket is closed
    $isClosed = (bool) ($data->status == "Closed");

    // set the disabled status
    $disabled = $isClosed ? "disabled='disabled'" : null;

    $response->title = $data->subject;
}

// access permissions check
$response->html = '
<section class="section">
    <div class="section-header">
        <h1>Knowledge Base '.(empty($knowledge_id)  ? '<small>Wants to Perform a Task? This will will guide your through.</small>' : null).'</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            '.($knowledge_id ? '<div class="breadcrumb-item"><a href="'.$baseUrl.'knowledgebase">Knowledge Base</a></div>' : '<div class="breadcrumb-item">Knowledge Base</div>').'
            '.($knowledge_id && !empty($support_array) ? '<div class="breadcrumb-item active">'.$data->subject.'</div>' : null).'
        </div>
    </div>
    <div class="row">
        
        <div class="col-12 col-sm-12 col-lg-12">
            '.(!$item_found && $isPermitted ? '
            <div class="text-right mb-2">
                <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#tickets" href="#"><i class="fa fa-plus"></i> Submit New Ticket</a>
            </div>' : null).'';

            if(!$item_found) {
                $response->html .= 
                '<div class="row">
                    <div class="col-md-'.($isSupport ? 7 : 9).'">
                        <div class="mb-3" id="support_search_input">
                            <label>Filter by Subject</label>
                            <input type="search" placeholder="Search by subject name" name="subject_title" class="form-control">
                            <input type="hidden" value="true" name="auto_search">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Filter by Section:</label>
                        <select id="support_filter_section" data-width="100%" class="selectpicker form-control">
                            <option value="">Please Select Section of App</option>';
                            foreach($myClass->support_sections as $value => $section) {
                                $response->html .= "<option value='{$value}'>{$section}</option>";
                            }
                        $response->html .= '
                        </select>
                    </div>
                    '.($isSupport ? '
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="text-white">Filter</label>
                                <button onclick="return load(\'article\')" class="btn btn-block btn-outline-success"><i class="fa fa-book-open"></i> Add Article</button>
                            </div>
                        </div>' : null
                    ).'
                    <div id="no_article_found" class="hidden col-lg-12 text-center">
                        <div class="card">
                            <div class="card-body text-danger">
                                Sorry! No article has been uploaded the moment. Please check back later.
                            </div>
                        </div>
                    </div>
                    '.$knowledge_base_list.'
                </div>';
            } else {
            $response->html .= '
                <div class="card mb-2">
                    <div class="card-header bg-teal">
                        <div class="row" style="width:100%">
                            <div class="col-md-8"><h4 class="card-title text-white">Article #'.$data->id.' - '.$data->subject.'</h4></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-lg-3 text-center my-2 my-md-0">
                                <div>
                                    <i class="font-20 fa fa-info-circle text-success"></i> 
                                    <h5 class="t-font-boldest text-15 mt-1 mb-0">
                                    '.$data->status.'
                                    </h5>
                                    <span>Status</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3 my-2 text-center my-md-0">
                                <div>
                                    <i class="font-20 far fa-clock text-warning"></i>
                                    <h5 class="t-font-boldest text-15 mt-1 mb-0">'.time_diff($data->date_updated).'</h5>
                                    <span>Last Updated</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3 my-2 text-center my-md-0">
                                <div>
                                    <i class="font-20 fa fa-phone text-primary"></i>
                                    <h5 class="t-font-boldest text-15 mt-1 mb-0">'.$data->section.'</h5>
                                    <span>Section</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3 my-2 text-center my-md-0">
                                <div>
                                    <i class="font-20 fa fa-calendar text-info"></i>
                                    <h5 class="t-font-boldest text-15 mt-1 mb-0">'.$data->date_created.'</h5>
                                    <span>Submitted On</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    '.(!$disabled ? '<button class="btn bg-teal"><i></i> Reply to Article</button>' : null).'
                    '.(!$disabled && $isPermitted ? '<button onclick="return modify_ticket(\'close\',\''.$knowledge_id.'\',\'knowledgebase\')" class="btn btn-danger">Close</button>' : null).'
                </div>
                <div class="mt-4">
                    <div class="activities">
                        <div class="activity">
                            <div class="activity-icon bg-primary text-white">
                                <img class="rounded-circle author-box-picture" width="55px" src="'.$baseUrl.''.$data->user_info->image.'">
                            </div>
                            <div class="activity-detail" style="width:100%">
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <div class="font-weight-bold text-primary">'.$data->user_info->name.'</div>
                                        <div>
                                            <span class="text-job font-13 text-primary">'.$data->date_created.'</span>
                                        </div>
                                    </div>
                                </div>
                                <div>'.($isSupport ? $data->content : auto_link($data->content, "url")).'</div>
                                <div>'.(!empty($data->video_link) ? "<div class='mt-2'>".iframe_holder($data->video_link, "550")."</div>" : null).'</div>
                                <div>'.$data->attachment_html.'</div>
                            </div>
                        </div>
                    </div>
                </div>'; 
            }

            
            // if a single reply id was parsed
            if($item_found) {

                // start the activities list
                $response->html .= '<div class="activities">';
                
                // loop through the replies list
                foreach($data->replies as $reply) {

                    // append to the replies list   
                    $content = '<div class="activity">';

                        // run this section if the user_type is a normal user
                        if($reply->user_type === "support") {
                            $content .= '
                            <div class="activity-icon bg-primary text-white">
                                <img class="rounded-circle author-box-picture" width="55px" src="'.$baseUrl.''.$reply->user_info->image.'">
                            </div>
                            <div class="activity-detail" style="width:100%">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div class="font-weight-bold text-primary">'.$reply->user_info->name.'</div>
                                        <div>
                                            <span class="text-job font-13 text-primary">'.$reply->date_created.'</span>
                                        </div>
                                    </div>
                                </div>
                                <div>'.$reply->content.'</div>
                            </div>';
                        } else {
                            // if the user_type is an support admin
                            $content .= '
                            <div class="d-flex justify-content-between" style="width:100%;border-radius:10px;">
                                <div class="card" style="width:100%;border-radius:10px;">
                                    <div class="card-body border-radius p-3 bg-green">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold text-white">'.$reply->user_info->name.'</div>
                                            <div>
                                                <span class="text-job font-13 text-white">'.$reply->date_created.'</span>
                                            </div>
                                        </div>
                                        <div class="mt-2">'.$reply->content.'</div>
                                    </div>
                                </div>
                                <div>
                                    <img class="rounded-circle author-box-picture" width="55px" src="'.$baseUrl.''.$reply->user_info->image.'">
                                </div>
                            </div>';
                        }

                    $content .= '</div>';

                    $response->html .= $content;
                }
                $response->html .= '</div>';
            }

            $response->html .= $item_found && !$disabled ? '
            <div class="mb-4">
                <div id="ticket_form" class="mt-4 p-0">
                    <div class="form-group mb-0">
                        <label>Comments</label>
                        <input type="hidden" hidden name="knowledge_id" id="knowledge_id" value="'.$data->id.'">
                        <textarea placeholder="Write here..." name="content" id="content" class="form-control"></textarea>
                    </div>      
                </div>
                <div class="mt-2">
                    <button onclick="return reply_ticket(\''.$data->id.'\',\'knowledgebase\')" class="btn btn-success"><i class="fa fa-reply-all"></i> Send</button>
                </div>
            </div>' : null;

            $response->html .= '
            </div>
    </div>

</section>';

// print out the response
echo json_encode($response);
?>