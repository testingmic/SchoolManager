<?php
// set the names of the directories
$system_folder = "system";
$application_folder = "application";

// session_start();

// display errors
error_reporting(E_ALL);

// set new places for my error recordings
ini_set("log_errors","1");
ini_set("error_log", "errors_log");

// Path to the system directory
define('BASEPATH', $system_folder.DIRECTORY_SEPARATOR);
define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
define('VIEWPATH', $application_folder.DIRECTORY_SEPARATOR);

function redirect_to_https() {
	if($_SERVER["SERVER_PORT"] !==433 && (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"]=="off")) {
		// header("Location: https://".$_SERVER["HTTP_HOST"].''.$_SERVER["REQUEST_URI"]."");
	}
}

redirect_to_https();

/*
	replace array indexes:
	1) fix windows slashes
	2) strip up-tree ../ as possible hack attempts
*/
$URL = STR_REPLACE( ARRAY( '\\', '../'), ARRAY( '/',  '' ), $_SERVER['REQUEST_URI'] );

//strip all forms of get data
IF ($offset = STRPOS($URL, '?')) { $URL = SUBSTR($URL, 0, $offset); } ELSE IF ($offset = STRPOS($URL, '//')) {
	$URL = SUBSTR($URL, 0, $offset);
}

// call the main core function and start processing your document
REQUIRE "system/core/myschoolgh.php";

// Load the models class
load_class('models', 'models');

/*
	the path routes below aren't just handy for stripping out
	the REQUEST_URI and looking to see if this is an attempt at
	direct file access, they're also useful for moving uploads,
	creating absolute URI's if needed, etc, etc
*/
$chop = -STRLEN(BASENAME($_SERVER['SCRIPT_NAME']));
define('DOC_ROOT', SUBSTR($_SERVER['SCRIPT_FILENAME'], 0, $chop));
define('URL_ROOT', SUBSTR($_SERVER['SCRIPT_NAME'], 0, $chop));

// strip off the URL root from REQUEST_URI
IF (URL_ROOT != '/') $URL = SUBSTR($URL, STRLEN(URL_ROOT));

// strip off excess slashes
$URL = TRIM($URL, '/');

// 404 if trying to call a real file
IF ( FILE_EXISTS(DOC_ROOT.'/'.$URL) && ($_SERVER['SCRIPT_FILENAME'] != DOC_ROOT.$URL) && ($URL != '') && ($URL != 'index.php') )
	die(no_file_log());

/*
	If $url is empty of default value, set action to 'default'
	otherwise, explode $URL into an array
*/
$SITEURL = (($URL == '') || ($URL == 'index.php') || ($URL == 'index.html')) ? ARRAY('index') : EXPLODE('/', html_entity_decode($URL));

/*
	I strip out non word characters from $SITEURL[0] as the include
	which makes sure no other oddball attempts at directory
	manipulation can be done. This means your include's basename
	can only contain a..z, A..Z, 0..9 and underscore!
	
	for example, as root this would make:
	pages/default.php
*/

// call the user logged in class
$defaultUser = (object) [];
$myClass = load_class('myschoolgh', 'models');
$usersClass = load_class('users', 'controllers');
$accessObject = load_class('accesslevel', 'controllers');
$noticeClass = load_class('notification', 'controllers');

// Check the site status
GLOBAL $SITEURL, $session;

// if the session is set
if(!empty($session->userId)) {
	
	// the query parameter to load the user information
	$i_params = (object) [
		"limit" => 1, 
		"user_id" => $session->userId, 
		"minified" => "simplified", 
		"append_wards" => true, 
		"filter_preferences" => true, 
		"userId" => $session->userId, 
		"append_client" => true, 
		"user_status" => ["Pending", "Active"]
	];
	$defaultUser = $usersClass->list($i_params)["data"];

	// if the result is not empty
	if(!empty($defaultUser)) {
		
		// get the current user information
		$defaultUser = $defaultUser[0];
		$defaultAcademics = $defaultUser->client->client_preferences->academics;
		
		// set the parameters for the access object
		$accessObject->userId = $defaultUser->user_id;
		$accessObject->clientId = $defaultUser->client_id;
		$accessObject->userPermits = json_decode($defaultUser->user_permissions);
		$accessObject->appPrefs = $defaultUser->client->client_preferences;
		$defaultUser->appPrefs = $defaultUser->client->client_preferences;
		$defaultUser->appPrefs->termEnded = (bool) (strtotime($defaultUser->appPrefs->academics->term_ends) < strtotime(date("Y-m-d")));
		
		// set additional parameters
		$isAdmin = (bool) ($defaultUser->user_type == "admin");
		$isTeacher = $isTutor = (bool) ($defaultUser->user_type == "teacher");
		$isParent = (bool) ($defaultUser->user_type == "parent");
		$isStudent = (bool) ($defaultUser->user_type == "student");
		$isEmployee = (bool) ($defaultUser->user_type == "employee");
		$isTutorAdmin = (bool) in_array($defaultUser->user_type, ["teacher", "admin"]);
		$isTutorStudent = (bool) in_array($defaultUser->user_type, ["teacher", "student"]);
		$isWardParent = (bool) in_array($defaultUser->user_type, ["parent", "student"]);
		$isWardTutorParent = (bool) in_array($defaultUser->user_type, ["teacher", "parent", "student"]);
		$isAdminAccountant = (bool) in_array($defaultUser->user_type, ["accountant", "admin"]);
	}
}

// To be used for inserting additional scripts
$loadedCSS = [];
$loadedJS = [];

// default file to include
$defaultFile = config_item('default_view_path').strtolower(preg_replace('/[^\w_]-/','',$SITEURL[0])).'.php';
$mainFile = config_item('default_view_path').'main.php';
$errorFile = config_item('default_view_path').'404.php';


// confirm if the first index has been parsed
// however the api and auth endpoint are exempted from this file traversing loop
if(isset($SITEURL[1]) && (!in_array($SITEURL[0], ["api", "auth", "history", "payment_checkout"]))) {

	// default file to include
	$otherFile = config_item('default_view_path').strtolower(preg_replace('/[^\w_]-/','',$SITEURL[1])).'.php';

	// include the file
	if(is_file($otherFile) and file_exists($otherFile)) {
		include($otherFile);
	} elseif(is_file($defaultFile) and file_exists($defaultFile)) {
		include($defaultFile);
	} else {
		no_file_log();
	}
} 
// confirm if the first index has been parsed
elseif(is_file($defaultFile) and file_exists($defaultFile)) {
	include($defaultFile);
} else {
	no_file_log();
}
?>