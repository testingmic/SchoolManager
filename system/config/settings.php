<?php 
// set the names of the directories
$system_folder = "system";
$application_folder = "application";

// display errors
error_reporting(E_ALL);
ini_set("display_errors", 0);

ini_set("log_errors","1");
ini_set("error_log", "errors_log");

// Path to the system directory
define('BASEPATH', $system_folder.DIRECTORY_SEPARATOR);
define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
define('VIEWPATH', $application_folder.DIRECTORY_SEPARATOR);

function loggedIn() {
    global $session;
    return ($session->userLoggedIn && $session->userId) ? true : false;
}

function invalid_route($title = "Invalid Route", $content = "Sorry! You are trying to access an invalid route on this server.") {
    echo "
    <!DOCTYPE html>
	<html lang='en'>
	<head>
	<title>{$title}</title></head>
	<body>
		<div style='margin: 0; font-family: -apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,\"Noto Sans\",sans-serif,\"Apple Color Emoji\",\"Segoe UI Emoji\",\"Segoe UI Symbol\",\"Noto Color Emoji\";
		    font-size: 1rem; font-weight: 400; line-height: 1.5; color: #212529; text-align: left;
		    background-color: #fff;'>
		    <div align='center' style='background:#fbfbfb; padding:20px; width: 90%; margin:auto auto;border: solid 1px #ccc;'>
		        <h1 style='color: #dc3545;font-size: 2.5rem;margin-bottom:0px;'>MySchoolGH</h1>
		        <h1 style='margin-bottom: 0.5rem;margin:0px;line-height: 1.2;font-size: 2.5rem;'>{$title}</h1>
		        <p style='font-size:20px'>{$content}</p>
		    </div>
		</div>
	</body>
	</html>";
}

// if the host is api.myschoolgh.com and the user tries to access it in the browser
if(($_SERVER['HTTP_HOST'] === 'api.myschoolgh.com') && !isset($_SERVER['HTTP_AUTHORIZATION'])) {
    die(invalid_route());
} elseif(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    function redirect_to_https() {
        if($_SERVER["SERVER_PORT"] !==433 && (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"]=="off")) {
            // header("Location: http://".$_SERVER["HTTP_HOST"].''.$_SERVER["REQUEST_URI"]."");
        }
    }
    redirect_to_https();
}

function run($isNotRemote, $return = false, $SITEURL =  []) {

	if(!empty($return)) {

		$URL = STR_REPLACE( ARRAY( '\\', '../'), ARRAY( '/',  '' ), $_SERVER['REQUEST_URI'] );

		if ($offset = strpos($URL, '?')) {
			$URL = SUBSTR($URL, 0, $offset);
		} else if ($offset = strpos($URL, '//')) {
		    $URL = SUBSTR($URL, 0, $offset);
		}

		$chop = -STRLEN(BASENAME($_SERVER['SCRIPT_NAME']));
		define('DOC_ROOT', SUBSTR($_SERVER['SCRIPT_FILENAME'], 0, $chop));
		define('URL_ROOT', SUBSTR($_SERVER['SCRIPT_NAME'], 0, $chop));

		if (URL_ROOT != '/') $URL = SUBSTR($URL, STRLEN(URL_ROOT));

		$URL = TRIM($URL, '/');

		if ( FILE_EXISTS(DOC_ROOT.'/'.$URL) && ($_SERVER['SCRIPT_FILENAME'] != DOC_ROOT.$URL) && ($URL != '') && ($URL != 'index.php') )
		    die(no_file_log());

		$SITEURL = (($URL == '') || ($URL == 'index.php') || ($URL == 'index.html')) ? ARRAY('index') : EXPLODE('/', html_entity_decode($URL));

		// set the site url
		$SITEURL = array_map("xss_clean", $SITEURL);

		// redirect all requests via curl to api url
		if(!$isNotRemote) {
		    $SITEURL[2] = $SITEURL[1] ?? null;
		    $SITEURL[1] = $SITEURL[0];
		    $SITEURL[0] = 'api';
		}

		return $SITEURL;

	}

	// default file to include
	$defaultFile = config_item('default_view_path').strtolower(preg_replace('/[^\w_]-/','',$SITEURL[0])).'.php';

	// get the request method that was parsed by the user
	$method = strtoupper( $_SERVER["REQUEST_METHOD"] );

	// confirm if the first index has been parsed
	// however the api and auth endpoint are exempted from this file traversing loop
	if(isset($SITEURL[1]) && (!in_array($SITEURL[0], ["api", "auth", "history", "payment_checkout", "download"]))) {

	    // default file to include
	    $otherFile = config_item('default_view_path').strtolower(preg_replace('/[^\w_]-/','',$SITEURL[1])).'.php';

	    // include the file
	    if(is_file($otherFile) and file_exists($otherFile)) {
	        return ['file' => $otherFile, 'url' => $SITEURL];
	    } elseif(is_file($defaultFile) and file_exists($defaultFile)) {
	        return ['file' => $defaultFile, 'url' => $SITEURL];
	    } else {
	        // if the request method 
	        if($method === "POST") {
	            no_file_log();
	        } else {
	            invalid_route();
	        }
	    }
	}

	// confirm if the first index has been parsed
	elseif(is_file($defaultFile) and file_exists($defaultFile)) {
	    return ['file' => $defaultFile, 'url' => $SITEURL];
	} else {
	    // if the request method 
	    if($method === "POST") {
	        no_file_log();
	    } else {
	        invalid_route();
	    }
	}
}