<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultClientData;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "ID Card Settings";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!$accessObject->hasAccess("view", "id_cards")) {
    // unset the page additional information
    $response->page_programming = [];
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// get the card settings
$cardSettings = $defaultClientData->client_preferences->id_card ?? [];
$defaultClientData->baseUrl = $baseUrl;

$response->scripts = ["assets/js/settings.js"];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-user-plus"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'card_generated">Generated Cards</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">ID Card Field Configuration</h4>
                    </div>
                    <div class="card-body">
                        <form class="ajax-data-form" action="'.$baseUrl.'api/account/update_card_settings" method="post" id="ajax-data-form-content">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="front_color">Front Card Background Color</label>
                                        <input onchange="return update_card_preview()" type="color" class="form-control" value="'.($cardSettings->front_color ?? "#1E40AF").'" id="front_color" name="front_color">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="back_color">Back Card Background Color</label>
                                        <input onchange="return update_card_preview()" type="color" class="form-control" value="'.($cardSettings->back_color ?? "#DC2626").'" id="back_color" name="back_color">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="front_text_color">Front Card Text Color</label>
                                        <input onchange="return update_card_preview()" type="color" class="form-control" value="'.($cardSettings->front_text_color ?? "#ffffff").'" id="front_text_color" name="front_text_color">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="back_text_color">Back Card Text Color</label>
                                        <input onchange="return update_card_preview()" type="color" class="form-control" value="'.($cardSettings->back_text_color ?? "#ffffff").'" id="back_text_color" name="back_text_color">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="back_found_message">Back Found Message</label>
                                        <textarea onkeyup="return update_card_preview()" class="form-control" name="back_found_message" id="back_found_message" rows="3">'.($cardSettings->back_found_message ?? card_found_message($defaultClientData->client_name)).'</textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="contact_numbers">Contact Numbers</label>
                                        <input onkeyup="return update_card_preview()" type="text" class="form-control" value="'.($defaultClientData->client_contact ?? "").'" id="contact_numbers" name="contact_numbers">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-lg-12 col-md-12 mt-3 text-center border-top pt-3">
                                    <button type="button-submit" class="btn btn-outline-success"><i class="fa fa-save"></i> Save Record</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Live Preview</h4>
                    </div>
                    <div class="card-body">
                    
                        '.render_card_preview($cardSettings, $defaultClientData).'

                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>