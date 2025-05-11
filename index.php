<?php
#display errors
error_reporting(E_ALL);

// display errors if the host is localhost
ini_set("display_errors", 1);

#set new places for my error recordings
ini_set("log_errors","1");
ini_set("error_log", "errors_log");

define('ROOTPATH', __DIR__);

// require the autoload for composer packages
require_once ROOTPATH . "/vendor/autoload.php";

define('MODELS_PATH', __DIR__ . "/application/models");
define('CONTROLLERS_PATH', __DIR__ . "/application/controllers");

// include the settings file
require_once ROOTPATH . "/system/config/settings.php";

if(file_exists("system/core/myschoolgh.php")) {
    require_once "system/core/myschoolgh.php";
} else {
    die(invalid_route("Setup Misconfiguration!", "Oopps! Server misconfiguration."));
}

// init variable
$isNotRemote = false;
$dataParam = $_GET + $_POST;

// if there was no cookie set
if (!isset($_SERVER['HTTP_AUTHORIZATION']) || !isset($dataParam['access_token']) || !isset($dataParam['remote'])) {
    $isNotRemote = true;
    $session = load_class('Session', 'libraries/Session');
}

global $session;

// Load the models class
load_class('models', 'models');

// set the site url
$SITEURL = run($isNotRemote, true, [], $argv ?? []);

// call the user logged in class
$academicSession = "Term";
$defaultUser = (object) [];
$defaultClientData = (object) [];
$myClass = load_class('myschoolgh', 'models');
$noticeClass = load_class('notification', 'controllers');
$accessObject = load_class('accesslevel', 'controllers');

global $isSupportPreviewMode, $isSupport;

// if the session is set
if(!empty($session->userId) && empty($argv)) {

    // use a new client id 
    if($session->previewMode && $session->previewClientId) {
        $session->clientId = $session->previewClientId;
    }

    // get the client data
    $defaultClientData = $myClass->client_session_data($session->clientId, false);
    
    // parse the client data
    $init_param = (object) ["client_data" => $defaultClientData];
    
    // the query parameter to load the user information
    $user_params = (object) [
        "limit" => 1, 
        "user_id" => $session->userId, 
        "minified" => "simplified", 
        "append_wards" => true, 
        "filter_preferences" => true, 
        "userId" => $session->userId, 
        "append_client" => true, 
        "user_status" => $myClass->allowed_login_status
    ];
    $usersClass = load_class('users', 'controllers', $init_param);
    $defaultUser = $usersClass->list($user_params)["data"];
    $defaultAcademics = (object)[];

    // set the client preferences
    $clientPrefs = $defaultClientData->client_preferences ?? [];
    $academicSession = $clientPrefs->sessions->session ?? "Term";

    // call the accepted period method and set the session name to use
    $myClass->accepted_period($academicSession);

    // This is set when the admin sets the academic year and term to only that has already been closed.
    $isReadOnly = $session->is_only_readable_app;
    
    // if the result is not empty
    if(!empty($defaultUser)) {
        
        // get the current user information
        $defaultUser = $defaultUser[0];

        // if the user id has not been set
        if(!isset($defaultUser->user_id)) {
            die(invalid_route("Database Misconfiguration!", "Oopps! There seems to be a misconfiguration with the system database tables."));
        }
                
        // set the parameters for the access object
        $accessObject->userId = $defaultUser->user_id;
        $accessObject->clientId = $defaultUser->client_id;
        $accessObject->userPermits = !is_array($defaultUser->user_permissions) ? json_decode($defaultUser->user_permissions, true) : $defaultUser->user_permissions;
        $accessObject->appPrefs = $clientPrefs;
        $defaultUser->appPrefs = $clientPrefs;
        $defaultUser->isPreviewMode = false;
        $defaultUser->appPrefs->isPreviewMode = false;
        
        // set additional parameters
        $isSupport = (bool) ($defaultUser->user_type == "support");
        $isSchool = "School";

        // set new variables
        $isEmployee = (bool) ($defaultUser->user_type == "employee");
        $isTutor = (bool) in_array($defaultUser->user_type, ["teacher"]);
        $isTutorAdmin = (bool) in_array($defaultUser->user_type, ["teacher", "admin"]);
        $isTutorStudent = (bool) in_array($defaultUser->user_type, ["teacher", "student"]);
        $isWardParent = (bool) in_array($defaultUser->user_type, ["parent", "student"]);
        $isWardTutorParent = (bool) in_array($defaultUser->user_type, ["teacher", "parent", "student"]);
        $isAdminAccountant = (bool) in_array($defaultUser->user_type, ["accountant", "admin"]);
        $isPayableStaff = (bool) in_array($defaultUser->user_type, ["accountant", "admin", "teacher", "employee"]);
        $isAccountant = (bool) in_array($defaultUser->user_type, ["accountant"]);
        $isAdmin = (bool) ($defaultUser->user_type == "admin");

        // set the features
        $clientFeatures = !empty($clientPrefs->features_list) ? (array) $clientPrefs->features_list : [];

        // if the user is not support then run this section
        if(!$isSupport) {

            // set additional parameters
            $defaultCurrency = $defaultClientData->client_preferences->labels->currency ?? null;
            $isTeacher = $isTutor = (bool) ($defaultUser->user_type == "teacher");
            $isParent = (bool) ($defaultUser->user_type == "parent");
            $isStudent = (bool) ($defaultUser->user_type == "student");

            // set this as init
            $defaultUser->appPrefs->termEnded = false;
            
            // if academics is set
            if(isset($defaultClientData->client_preferences->academics)) {
                // set the default academics information
                $defaultAcademics = $defaultClientData->client_preferences->academics;
                
                // reset the academic year and term if the session variables are not empty
                if(!empty($session->is_only_readable_app)) {
                    $defaultAcademics->academic_year = $session->is_readonly_academic_year;
                    $defaultAcademics->academic_term = $session->is_readonly_academic_term;
                    $defaultAcademics->term_starts = $session->is_readonly_term_starts;
                    $defaultAcademics->term_ends = $session->is_readonly_term_ends;
                    $defaultAcademics->year_starts = $session->is_readonly_year_starts;
                    $defaultAcademics->year_ends = $session->is_readonly_year_ends;
                }

                // set the term ended variable
                $defaultUser->appPrefs->termEnded = (bool) (strtotime($defaultUser->appPrefs->academics->term_ends) < strtotime(date("Y-m-d")));
            }
        }

    }

    // set the isNotRemote variable
    $isNotRemote = false;

    if($session->previewMode) {
        $isSupport = false;
        $isAdmin = true;
        $isTutorAdmin = true;
        $isPayableStaff = true;
        $isAdminAccountant = true;
        $isSupportPreviewMode = true;

        // set the preview mode
        $defaultUser->isPreviewMode = true;
        $defaultUser->appPrefs->isPreviewMode = true;
    }
    
}

// To be used for inserting additional scripts
$loadedCSS = [];
$loadedJS = [];

$settings = run($isNotRemote, false, $SITEURL, $argv ?? []);

if(is_array($settings)) {
	include($settings['file']);
}
?>