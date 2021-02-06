<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "E-Learning Material";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/elearning.js"];

// load the resource
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

// item_id 
$item = explode("_", $item_id);

// if the user id is not empty
if(empty($item_id) || !isset($item[1])) {
    $response->html = page_not_found();
} else {

    // get parameters
    $resourceObj = load_class("resources", "controllers");
    $autoplay = (bool) isset($_GET["autoplay"]);

    // confirm if the video_id and video_time were parsed
    if(isset($_POST["video_id"]) && isset($_POST["video_time"])) {
        // conver the item into an object
        $params = (object) array_map("xss_clean", $_POST);
        $params->userId = $defaultUser->user_id;

        // save the time for this video 
        $resourceObj->save_time($params);
    }

    // param
    $params = (object) [
        "limit" => 1,
        "clientId" => $clientId,
        "userId" => $defaultUser->user_id,
        "resource_id" => $item[0],
        "clean_response" => true,
        "userData" => $defaultUser
    ];

    $resource_list = $resourceObj->e_resources($params);
    
    // end the query if the file was not found
    if(!isset($resource_list["files"])) {
        // page not found
        $response->html = page_not_found();
        echo json_encode($response);
        exit;
    }
    // continue if all is set
    $video = null;
    $other_videos = [];
    foreach($resource_list["files"] as $resource) {
        if($resource->unique_id == $item[1]) {
            $video = $resource;
        } else {
            $other_videos[] = $resource;
        }
    }

    $related_videos = "<div class='text-center font-italic card-body mb-0 pb-0 pt-2'>There are no related videos to the current one.</div>";
    $file_content = "<div class='text-center font-italic card-body'>No resource was foud for the id parsed</div>";

    // vid parameter to use for the query
    $video_param = (object) ["userId" => $defaultUser->user_id];
    
    // if the data is empty
    if(empty($video)) {
        $response->html = page_not_found();
    } else {

        // get the learning
        $elearning = $resource_list["data"][0];

        $video_param->video_id = "{$video->record_id}_{$video->unique_id}";

        // get the video timer
        $timer = $resourceObj->video_time($video_param);

        // video informat
        $video_mime = ["mp4", "mpeg", "movie", "webm", "mov", "mpg", "mpeg", "qt"];
        $isVideo = in_array($video->type, $video_mime);
       
        $file_content = "
            <div class=\"card-body p-0\">
                <video data-video_unique_id='{$video->record_id}_{$video->unique_id}' id='elearning_video' ".($autoplay ? "autoplay='true'" : null)." style='display: block; cursor:pointer; width:100%;' controls='true' src='{$baseUrl}{$video->path}#t={$timer}'></video>
            </div>
            <div class=\"card-footer border-top p-2\">
                <div class=\"row pr-0  border-bottom\">
                    <div class=\"col-lg-7\">
                        <h5 class=\"pb-2\">{$elearning->subject}
                            ".($elearning->state === "Draft" ? 
                                "<span class='tx-12 badge badge-primary'>Draft</span>" : 
                                "<span class='tx-12 badge badge-success'>Active</span>"
                            )."
                        </h5>
                    </div>
                    <div class=\"col-lg-5 text-right\">
                        <span><strong>{$video->name}</strong></span> <span class=\"text-muted tx-11\">({$video->size})</span><br>
                        <strong>{$video->uploaded_by}</strong>
                    </div>
                </div>
                <div class=\"row mt-2 pb-2\">
                    <div class=\"col-lg-8\">
                        <span><i class=\"fa fa-home\"></i> {$elearning->course_name}</span> | 
                        <span><i class=\"fas fa-graduation-cap\"></i> {$elearning->class_name}</span> |
                        <span><i class=\"fa fa-book-open\"></i> {$elearning->unit_name}</span>
                    </div>
                    <div class=\"text-right col-lg-4\">
                        ".($elearning->created_by === $defaultUser->user_id ? 
                            "<a class='btn btn-sm btn-outline-success' href='{$baseUrl}e-learning_update/{$elearning->item_id}'><i class='fa fa-edit'></i> Update Details</a>" : ""
                        )."
                    </div>
                </div>
                <div class=\"file_caption pt-3 pb-3 border-top text-left\">
                    {$elearning->description}
                </div>
            </div>
        ";

        // if the other related videos arent empty
        if(!empty($other_videos)) {
            
            // begin the variable
            $related_videos = "";

            // loop through the other attached videos
            foreach($other_videos as $video) {
                $time = time_diff($video->datetime);
                // set a new unique id
                $video_param->video_id = "{$video->record_id}_{$video->unique_id}";

                // get the video timer
                $timer = $resourceObj->video_time($video_param);

                $related_videos .= "
                <div class='mb-2 p-0' style='border-radius:0px 0px 10px 10px'>
                    <div class='card-body p-0 m-0' id='related_video'>
                        <video width='100%' data-href='{$baseUrl}e-learning_view/{$video->record_id}_{$video->unique_id}?autoplay=true' data-src='{$baseUrl}{$video->path}#t={$timer}' title='Click to watch the video: {$video->name}' height='100%' class='cursor' src='{$baseUrl}{$video->path}#t={$timer}'></video>
                    </div>
                    <div class='card-footer pl-2 pr-2 pt-0 pb-0 m-0'>
                        <h6 class='p-0 m-0'>{$elearning->subject}</h6>
                        <p style='line-height:13px' class='font-italic'>{$time}</p>
                    </div>
                </div>";
            }
        }

        $response->html = '
            <section class="section">
                <div class="section-header">
                    <h1>'.$pageTitle.'</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                        <div class="breadcrumb-item active"><a href="'.$baseUrl.'e-learning">E-Learning</a></div>
                        <div class="breadcrumb-item">'.$pageTitle.'</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-9 col-lg-9">
                        <div class="card mb-0">
                            '.$file_content.'
                        </div>
                        <div class="card-body mt-0 pr-0 pl-0">
                            <div id="video_comments">
                                <div class="share_public_comment">
                                    <div class="comments_counter mb-3">0 Comments</div>
                                    '.($elearning->allow_comments === "allow" ? 
                                        '<div class="d-flex justify-content-start" style="min-height:70px">
                                            <div class="pr-1"><img width="60px" class="rounded-circle cursor author-box-picture" src="'.$baseUrl.''.$defaultUser->image.'"></div>
                                            <div class="p-0" style="width:100%">
                                                <div id="public_comment" contenteditable="true" dir="auto" class="public_comment trix-slim-scroll" aria-label="Add a public comment..."></div>
                                                <div class="d-flex justify-content-between">
                                                    <div class="comment_response"></div>
                                                    <div class="text-right mt-2 hidden" id="public_comment_button">
                                                        <button id="cancel_button" onclick="cancel_comment()" disabled class="btn btn-sm btn-outline-danger">CANCEL</button>
                                                        <button id="share_comment" onclick="share_comment()" disabled class="btn btn-sm btn-outline-primary">COMMENT</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>' : 
                                        '<div class="alert alert-warning mb-0">Comments have been closed</div>'
                                    ).'
                                    <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$item_id.'" class="slim-scroll pt-3 mt-4 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                    <div class="text-center loader_display hidden"><i class="text-primary fa fa-spin fa-spinner fa-2x"></i></div>
                                    <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn hidden btn-sm btn-outline-secondary">Loading comments</button></div>    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3">
                        <div class="card">
                            <div class="card-body p-2">
                                <h5 class="mb-0 border-bottom pb-2">PLAYLIST</h5>
                                '.$related_videos.'
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