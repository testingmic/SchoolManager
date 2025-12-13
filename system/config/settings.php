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

function version() {
    return "1.9.1";
}

function is_localhost() {
	return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost']);
}

function loggedIn() {
    global $session;
    return ($session->userLoggedIn && $session->userId) ? true : false;
}

function invalid_route($title = "Invalid Route", $content = "Sorry! You are trying to access an invalid route on this server.") {

	$init_data = parse_ini_file(ROOT_DIRECTORY . "/db.ini");

    echo "
    <!DOCTYPE html>
	<html lang='en'>
	<head>
		<title>{$title}</title></head>
		<link rel='stylesheet' href='".$init_data['base_url']."assets/css/app.min.css'>
		<style>
			.main-wrapper {
				margin: 0; 
				font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';
				font-size: 1rem; 
				font-weight: 400; 
				line-height: 1.5; 
				color: #212529; 
				text-align: left;
				background-color: #fff;
			}
			.content {
				background:#fbfbfb; 
				padding:20px; 
				width: 90%; 
				text-align: center;
				border-radius: 10px;
				margin:auto auto;
				border: solid 1px #ccc;
			}
		</style>
	</head>
	<body>
		<div class='main-wrapper mt-3'>
		    <div class='content text-center'>
		        <h1 style='color: #dc3545;font-size: 2.5rem;margin-bottom:0px;'>MySchoolGH</h1>
		        <h1 style='margin-bottom: 0.5rem;margin:0px;line-height: 1.2;font-size: 2.5rem;'>{$title}</h1>
		        <p style='font-size:20px'>{$content}</p>
				<a href='".$init_data['base_url']."' class='btn btn-outline-success'>Back to Home</a>
		    </div>
		</div>
	</body>
	</html>";
}

// if the host is api.myschoolgh.com and the user tries to access it in the browser
if(isset($_SERVER['HTTP_HOST'])) {
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
}

function run($isNotRemote = false, $return = false, $SITEURL =  [], $argv = []) {

	if(!empty($return) && isset($_SERVER['REQUEST_URI'])) {

		$URL = str_replace( array( '\\', '../'), array( '/',  '' ), $_SERVER['REQUEST_URI'] );

		if ($offset = strpos($URL, '?')) {
			$URL = substr($URL, 0, $offset);
		} else if ($offset = strpos($URL, '//')) {
		    $URL = substr($URL, 0, $offset);
		}

		$chop = -strlen(basename($_SERVER['SCRIPT_NAME']));
		define('DOC_ROOT', substr($_SERVER['SCRIPT_FILENAME'], 0, $chop));
		define('URL_ROOT', substr($_SERVER['SCRIPT_NAME'], 0, $chop));

		if (URL_ROOT != '/') $URL = substr($URL, strlen(URL_ROOT));

		$URL = trim($URL, '/');

		if ( file_exists(DOC_ROOT.'/'.$URL) && ($_SERVER['SCRIPT_FILENAME'] != DOC_ROOT.$URL) && ($URL != '') && ($URL != 'index.php') )
		    die(no_file_log());

		$SITEURL = (($URL == '') || ($URL == 'index.php') || ($URL == 'index.html')) ? array('index') : explode('/', html_entity_decode($URL));

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

	if(!empty($argv)) {
		unset($argv[0]);
		return ['file' => CONTROLLERS_PATH . "/console.php", 'argv' => $argv];
	}

	// default file to include
	$notFoundFile = config_item('default_view_path').'not_found.php';
	$defaultFile = config_item('default_view_path').strtolower(preg_replace('/[^\w_]-/','', $SITEURL[0])).'.php';

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
	            return ['file' => $notFoundFile, 'url' => $SITEURL];
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
	        return ['file' => $notFoundFile, 'url' => $SITEURL];
	    }
	}
}