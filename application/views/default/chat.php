<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $session;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Live Chat";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/page/chat.js"];

$params = (object) [
    "clientId" => $clientId
];

$chatObj = load_class("chats", "controllers");
$recentChat = $chatObj->recent($session->userId);

// get users list
$users_list = '<li id="temp_user_list" class="clearfix"><div>No user to display for now.</div></li>';

// recent content
$lastChat = (object) [];

// if the messages is not empty
if(!empty($recentChat["messages"])) {
    $users_list = "";
    // loop through the messages list
    foreach($recentChat["messages"] as $key => $message_id) {
        foreach($message_id as $message) {
            if(isset($message->message_unique_id)) {
                $online_text = $message->receipient_info->online ? "online" : "offline";
                $online_msg = $message->receipient_info->online ? "Online" : "Left {$message->receipient_info->offline_ago}";
                $users_list .= '
                <li id="default_list" style="width:100%" data-message_id="'.$message->message_unique_id.'" onclick="return display_messages(\''.$message->message_unique_id.'\',\''.$message->receiver_id.'\',\''.$message->receipient_info->name.'\',\''.$message->receipient_info->image.'\',\''.$message->receipient_info->offline_ago.'\')" class="clearfix d-flex '.(($key === 0) ? "actdive" : "").'">
                    <img src="'.$baseUrl.''.$message->receipient_info->image.'" alt="avatar">
                    <div class="about" style="width:100%">
                        <div class="name">'.$message->receipient_info->name.'</div>
                        <div class="status">
                            <i class="material-icons '.$online_text.'">fiber_manual_record</i>
                            '.$online_msg.'
                            <span data-user_id="'.$message->receiver_id.'" class="float-right"></span>
                        </div>
                    </div>
                </li>';
            }
        }
    }
}

// get the content
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="card">
                  <div class="body">
                    <div id="plist" class="people-list">
                      <div class="chat-search">
                        <input type="text" id="search_user" class="form-control" placeholder="Search... (Hit enter to search)" />
                      </div>
                      <div class="m-b-20">
                        <div id="chat-scroll">
                          <ul class="chat-list list-unstyled m-b-0">
                            '.$users_list.'
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <div class="card">
                  <div class="chat">
                    <div class="chat-header clearfix">
                      '.(isset($lastChat->seen_time) ? "
                        <img src=\"{$baseUrl}{$message->receipient_info->image}\" alt=\"avatar\">
                        <div class=\"chat-about\">
                            <div class=\"chat-with\">{$message->receipient_info->name}</div>
                            <div class=\"chat-num-messages\">{$message->receipient_info->offline_ago}</div>
                        </div>
                        " : "").'
                    </div>
                  </div>
                  <div class="chat-box" id="mychatbox">
                    <div class="card-body chat-content"></div>
                    <div class="card-footer chat-form">
                      <form id="chat-form" method="POST">
                        <input id="chat-input" disabled type="text" class="form-control" placeholder="Type a message">
                        <button disabled class="btn btn-primary">
                          <i class="far fa-paper-plane"></i>
                        </button>
                      </form>
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