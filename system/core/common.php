<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Followin
 * @subpackage	Followin Super Class
 * @category	Core Functions
 * @author		Analitica Innovare Dev Team
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// define the available quick links
$availableQuickLinks = [
	"chat" => [
		"label" => "Chat", "href" => "chats",
		"favicon" => 'data-feather="message-square" class="icon-lg"',
	],
	"calendar" => [
		"label" => "Calendar", "href" => "calendar",
		"favicon" => 'data-feather="calendar" class="icon-lg"',
	],
	"adverts" => [
		"label" => "Adverts", "href" => "ads-view",
		"favicon" => 'data-feather="speaker" class="icon-lg"',
	]
];

// ------------------------------------------------------------------------

$language =	array();

if ( ! function_exists('is_php'))
{
	/**
	 * Determines if the current version of PHP is equal to or greater than the supplied value
	 *
	 * @param	string
	 * @return	bool	TRUE if the current version is $version or higher
	 */
	function is_php($version)
	{
		static $_is_php;
		$version = (string) $version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
		}

		return $_is_php[$version];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_really_writable'))
{
	/**
	 * Tests for file writability
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute. is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @link	https://bugs.php.net/bug.php?id=54709
	 * @param	string
	 * @return	bool
	 */
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') OR ! ini_get('safe_mode')))
		{
			return is_writable($file);
		}

		/* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand());
			if (($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('list_folder_items'))
{
	/**
	 * List folder items
	 *
	 * list_folder_items($directory) gets all items in a folder and displays them
	 *
	 * @param	string
	 * @return	files array 
	 */
	function list_folder_items($directory)
	{
		if($handle = opendir($directory)) {

			while(false !== ($entry = readdir($handle))) {
				
				if($entry != '.' && $entry != '..') {
					
					return $entry."<br>";
					
				}
			}
		}
	}
}

// ----------------------------------------------------------------------
/**
	 * String to Array Function
	 *
	 * This function acts as a singleton. If the string is not an array
	 * it returns the string as a array. Also if it is already an array 
	 * then it will return same to it. 
	 *
	 * @param	string	the class name being requested
	 * @param	string	the directory where the class should be found
	 * @param	mixed	an optional argument to pass to the class constructor
	 * @return	object
	 */
if ( ! function_exists('_str_to_array'))
{
	function _str_to_array($item_list)
	{
		if ( ! is_array($item_list))
		{
			return (strpos($item_list, ',') !== FALSE) ? preg_split('/[\s,]/', $item_list, -1, PREG_SPLIT_NO_EMPTY) : (array) trim($item_list);
		}

		return $item_list;
	}
}



/**
 * @method stringToArray
 * 
 * @desc Converts a string to an array
 * @param $string The string that will be converted to the array
 * @param $delimeter The character for the separation
 * 
 * @return Array
 */
function stringToArray($string, $delimiter = ",") {
	if(is_array($string)) {
		return $string;
	}

	$array = [];
	$expl = explode($delimiter, $string);
	foreach($expl as $each) {
		if(!empty($each)) {
			$array[] = trim($each);
		}
	}
	return $array;
}

// ------------------------------------------------------------------------

if ( ! function_exists('load_class'))
{
	/**
	 * Class registry
	 *
	 * This function acts as a singleton. If the requested class does not
	 * exist it is instantiated and set to a static variable. If it has
	 * previously been instantiated the variable is returned.
	 *
	 * @param	string	the class name being requested
	 * @param	string	the directory where the class should be found
	 * @param	mixed	an optional argument to pass to the class constructor
	 * @return	object
	 */
	function load_class($class, $directory = 'core', $param = null)
	{
		static $_classes = array();

		// Does the class exist? If so, we're done...
		if (isset($_classes[$class]))
		{
			return $_classes[$class];
		}

		$name = FALSE;
		
		// Look for the class in the native system/libraries folder
		foreach (array(BASEPATH, APPPATH) as $path) {
			
			if (file_exists($path.$directory.'/'.$class.'.php')) {
				$name = $class;

				if (class_exists($name, FALSE) === FALSE) {
					
					require_once($path.$directory.'/'.$class.'.php');
					
					break;
				} 
			}
		}
		
		// Did we find the class?
		if ($name === FALSE) {
			// Note: We use exit() rather than show_error() in order to avoid a
			// self-referencing loop with the Exceptions class
			echo 'Unable to locate the specified class: '.$class.'.php';
			exit(5); // EXIT_UNK_CLASS
		}
		
		// Keep track of what we just loaded
		is_loaded($class);
		
		// check if the class really exists
		if(class_exists($class)) {
			$_classes[$class] = isset($param) && !empty($param) ? new $name($param) : new $name();
			return $_classes[$class];
		} else {
			echo 'The specified class has not yet been defined: '.$class;
			exit;
		}
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('is_loaded'))
{
	/**
	 * Keeps track of which libraries have been loaded. This function is
	 * called by the load_class() function above
	 *
	 * @param	string
	 * @return	array
	 */
	function &is_loaded($class = '')
	{
		static $_is_loaded = array();

		if ($class !== '')
		{
			$_is_loaded[strtolower($class)] = $class;
		}

		return $_is_loaded;
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('load_core')) {
	
	function load_core($file, $directory = 'core') {		
		// Look for the class in the native system/helpers folder
		foreach (array(BASEPATH) as $path) {
			
			if (file_exists($path.$directory.'/'.$file.'.php')) {				
				require_once($path.$directory.'/'.$file.'.php');				
				break;
			} else {
				echo 'The core file with name '.$file.' does not exist.';
				exit(3); // EXIT_CONFIG
			}
		}
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('load_library')) {
	
	function load_library($file, $directory = 'libraries') {
		
		// Submit the file name to the _str_to_array($file) function 
		$file = _str_to_array($file);
		
		// Use foreach Loop to get the file 
		foreach($file as $filename) {
			
			// confirm that the strlen($filename) should be more
			// than 3 characters 
			if( strlen($filename) > 3 ) {
				if (file_exists(BASEPATH.$directory.'/'.$filename.'.php')) {				
					require_once(BASEPATH.$directory.'/'.$filename.'.php');				
				} else {
					echo 'The '.ucfirst($filename).' Library file does not exist.';
					exit(3); // EXIT_CONFIG
				}
			}
		}
		
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('load_helpers')) {
	
	function load_helpers($file, $directory = 'helpers') {		
		
		// Submit the file name to the _str_to_array($file) function 
		$file = _str_to_array($file);
		
		// Use foreach Loop to get the file 
		foreach($file as $filename) {
			
			// confirm that the strlen($filename) should be more
			// than 3 characters 
			if( strlen($filename) > 3 ) {
				if (file_exists(BASEPATH.$directory.'/'.$filename.'.php')) {				
					require_once(BASEPATH.$directory.'/'.$filename.'.php');				
				} else {
					echo 'The '.ucfirst($filename).' Helper file does not exist.';
					exit(3); // EXIT_CONFIG
				}
			}
			
		}
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('load_file')) {
	
	function load_file($file) {
		
		// Submit the file name to the _str_to_array($file) function 
		$file = _str_to_array($file);
		
		// Look for the class in the native system/helpers folder
		foreach($file as $filename=>$directory) {
			
			if (file_exists(BASEPATH.$directory.'/'.$filename.'.php')) {				
				require_once(BASEPATH.$directory.'/'.$filename.'.php');	
			} else {
				echo 'The '.$file.' file does not exist.';
				exit(3); // EXIT_CONFIG
			}
		}
	}
}


// ------------------------------------------------------------------------
if ( ! function_exists('load_lang')) {
	
	function load_lang($file, $directory = 'language/english') {
		
		// Submit the file name to the _str_to_array($file) function 
		$file = _str_to_array($file);
		
		// Look for the class in the native system/helpers folder
		foreach($file as $filename) {
			
			if (file_exists(BASEPATH.$directory.'/'.$filename.'_lang.php')) {				
				require_once(BASEPATH.$directory.'/'.$filename.'_lang.php');	
			} else {
				echo 'The '.$file.' language file does not exist.';
				exit(3); // EXIT_CONFIG
			}
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_config'))
{
	/**
	 * Loads the main config.php file
	 *
	 * This function lets us grab the config file even if the Config class
	 * hasn't been instantiated yet
	 *
	 * @param	array
	 * @return	array
	 */
	function &get_config(Array $replace = array()) {
		static $config;

		if (empty($config)) {
			$file_path = BASEPATH.'config/config.php';
			if (file_exists($file_path)) {
				require($file_path);
			}

			// Does the $config array exist in the file?
			if ( ! isset($config) OR ! is_array($config)) {
				echo 'Your config file does not appear to be formatted correctly.';
				exit(3);
			}
		}

		// Are any values being dynamically added or replaced?
		foreach ($replace as $key => $val) {
			$config[$key] = $val;
		}

		return $config;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('config_item')) {
	/**
	 * Returns the specified config item
	 *
	 * @param	string
	 * @return	mixed
	 */
	function config_item($item) {
		static $_config;

		if (empty($_config)) {
			// references cannot be directly assigned to static variables, so we use an array
			$_config[0] =& get_config();
		}

		return isset($_config[0][$item]) ? $_config[0][$item] : NULL;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_https')) {
	/**
	 * Is HTTPS?
	 *
	 * Determines if the application is accessed via an encrypted
	 * (HTTPS) connection.
	 *
	 * @return	bool
	 */
	function is_https()
	{
		if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'){
			return TRUE;
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
		{
			return TRUE;
		}
		elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
		{
			return TRUE;
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_cli'))
{

	/**
	 * Is CLI?
	 *
	 * Test to see if a request was made from the command line.
	 *
	 * @return 	bool
	 */
	function is_cli()
	{
		return (PHP_SAPI === 'cli' OR defined('STDIN'));
	}
}

function show_error($heading = 'Page Not Found', $message='Sorry the page you are trying to view does not exist on this server', $template = 'error_general', $status_code = 500)
{
	$ob_level = ob_get_level();
	
	$templates_path = config_item('error_views_path');
	if (empty($templates_path))
	{
		$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
	}

	if (is_cli())
	{
		$message = "\t".(is_array($message) ? implode("\n\t", $message) : $message);
		$template = 'cli'.DIRECTORY_SEPARATOR.$template;
	}
	else
	{
		$message = '<p>'.(is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
		$template = 'html'.DIRECTORY_SEPARATOR.$template;
	}
	
	if (ob_get_level() > $ob_level + 1)
	{
		ob_end_flush();
	}
	return include($templates_path.$template.'.php');
	exit;
}

// ------------------------------------------------------------------------

if ( ! function_exists('show_404'))
{
	/**
	 * 404 Page Handler
	 *
	 * This function is similar to the show_error() function above
	 * However, instead of the standard error template it displays
	 * 404 errors.
	 *
	 * @param	string
	 * @param	bool
	 * @return	void
	 */
	function show_404($page = '', $log_error = FALSE)
	{
		if (is_cli())
		{
			$heading = 'Not Found';
			$message = 'The controller/method pair you requested was not found.';
		}
		else
		{
			$heading = '404 Page Not Found';
			$message = 'The page you requested was not found.';
		}

		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', $heading.': '.$page);
		}

		echo show_error($heading, $message, 'error_404', 404);
		exit(4); // EXIT_UNKNOWN_FILE
	}
}


// ------------------------------------------------------------------------

if ( ! function_exists('log_message'))
{
	/**
	 * Error Logging Interface
	 *
	 * We use this as a simple mechanism to access the logging
	 * class and send messages to be logged.
	 *
	 * @param	string	the error level: 'error', 'debug' or 'info'
	 * @param	string	the error message
	 * @return	void
	 */
	function log_message($level, $message)
	{
		static $_log;

		if ($_log === NULL)
		{
			// references cannot be directly assigned to static variables, so we use an array
			$_log[0] = load_class('log', 'core');
		}

		$_log[0]->write_log($level, $message);
	}
}

function lang_load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
	
	global $language;
	
	$langfile = str_replace('.php', '', $langfile);

	if ($add_suffix === TRUE)
	{
		$langfile = preg_replace('/_lang$/', '', $langfile).'_lang';
	}

	$langfile .= '.php';

	if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom))
	{
		$config =& get_config();
		$idiom = empty($config['language']) ? 'english' : $config['language'];
	}

	// Load the base file, so any others found can override it
	$basepath = BASEPATH.'language/'.$idiom.'/'.$langfile;
	if (($found = file_exists($basepath)) === TRUE)
	{
		include($basepath);
	}

	// Do we have an alternative path to look in?
	if ($alt_path !== '')
	{
		$alt_path .= 'language/'.$idiom.'/'.$langfile;
		if (file_exists($alt_path))
		{
			include($alt_path);
			$found = TRUE;
		}
	}
	

	if ($found !== TRUE)
	{
		show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
	}

	if ($return === TRUE)
	{
		return $lang;
	}

	$language = array_merge($language, $lang);

	return TRUE;
}

function lang_line($line, $log_errors = TRUE, $filename = null) {
	
	lang_load($filename);
	
	global $language;
	
	$value = isset($language[$line]) ? $language[$line] : FALSE;

	// Because killer robots like unicorns!
	if ($value === FALSE && $log_errors === TRUE)
	{
		exit('Could not find the language line "'.$line.'"');
	}

	return $value;
}


function method($upper = FALSE)
{
	return ($upper)
	? strtoupper($_SERVER['REQUEST_METHOD'])
	: strtolower($_SERVER['REQUEST_METHOD']);
}
// ------------------------------------------------------------------------

// ------------------------------------------------------------------------

if ( ! function_exists('get_mimes'))
{
	/**
	 * Returns the MIME types array from config/mimes.php
	 *
	 * @return	array
	 */
	function get_mimes() {
		// static $_mimes;

		// if (empty($_mimes))
		// {
		// 	$_mimes = file_exists(APPPATH.'config/mimes.php')
		// 	? include(APPPATH.'config/mimes.php')
		// 	: array();

		// 	if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'))
		// 	{
		// 		$_mimes = array_merge($_mimes, include(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'));
		// 	}
		// }

		// return $_mimes;
	}
}
// ------------------------------------------------------------------------

if ( ! function_exists('function_usable'))
{
	/**
	 * Function usable
	 *
	 * Executes a function_exists() check, and if the Suhosin PHP
	 * extension is loaded - checks whether the function that is
	 * checked might be disabled in there as well.
	 *
	 * This is useful as function_exists() will return FALSE for
	 * functions disabled via the *disable_functions* php.ini
	 * setting, but not for *suhosin.executor.func.blacklist* and
	 * *suhosin.executor.disable_eval*. These settings will just
	 * terminate script execution if a disabled function is executed.
	 *
	 * The above described behavior turned out to be a bug in Suhosin,
	 * but even though a fix was committed for 0.9.34 on 2012-02-12,
	 * that version is yet to be released. This function will therefore
	 * be just temporary, but would probably be kept for a few years.
	 *
	 * @link	http://www.hardened-php.net/suhosin/
	 * @param	string	$function_name	Function to check for
	 * @return	bool	TRUE if the function exists and is safe to call,
	 *			FALSE otherwise.
	 */
	function function_usable($function_name)
	{
		static $_suhosin_func_blacklist;

		if (function_exists($function_name))
		{
			if ( ! isset($_suhosin_func_blacklist))
			{
				$_suhosin_func_blacklist = extension_loaded('suhosin')
				? explode(',', trim(ini_get('suhosin.executor.func.blacklist')))
				: array();
			}

			return ! in_array($function_name, $_suhosin_func_blacklist, TRUE);
		}

		return FALSE;
	}
}

function print_msg($type, $message) {
	print "<div class='alert alert-$type'>$message</div>";
}

function js_focus($div_id) {
	$js_script = "<script>";
	$js_script .= "$('html, body').animate({
        scrollTop: $('#scroll-top').offset().top
    }, 100);";
	$js_script .= "$('#$div_id').focus();";
	$js_script .= "</script>";
	print $js_script;
}

function sendJSON($data, $exit = false){
	if(!$exit) return json_encode($data);
	echo json_encode($data);
	exit;
}

/**
 * Set notification session
 * @param  string/array/object $message 
 * Notification message string or Array/Object
 * @param  string $type  type of message ie. error|success|warning|info
 * @return bool          message set in session?
 */
function set_notify($notification, $class = "error"){
	global $session;
	$type = gettype($notification);
	switch ($type) {
		case 'string':
		$message = $notification;
		break;

		case 'array':
		case 'object':
		$notification = (object) $notification;
		$message = $notification->mesage;
		$class = $notification->class;
		break;			
	}
	return $session->set_flashdata("notify", [$message, $class]);
}

function notify($returnVal = false){
	global $session;
	$notify = $session->flashdata("notify");

	if($returnVal) return $notify ?? [];

	if(empty($notify)) return null;
	
	switch ($notify[1]) {
		case 'info':
		case 'success':
			$notify_icon = "<i class='fa fa-check-circle-o'></i>";
			break;
		case 'error':
		case 'danger':
		case 'warning':
			$notify_icon = "<i class='fa fa-exclamation-triangle'></i>";
			break;
	}

	$div = "<div class='text-center alert alert-{$notify[1]}' style='display:block;width:inherit'>";
	$div .= $notify_icon.' '.$notify[0]."</div>";
	return $div;

}

if (! function_exists('dot_array_search'))
{
	/**
	 * Searches an array through dot syntax. Supports
	 * wildcard searches, like foo.*.bar
	 *
	 * @param string $index
	 * @param array  $array
	 *
	 * @return mixed|null
	 */
	function dot_array_search(string $index, array $array)
	{
		$segments = explode('.', rtrim(rtrim($index, '* '), '.'));

		return _array_search_dot($segments, $array);
	}
}

if (! function_exists('_array_search_dot'))
{
	/**
	 * Used by dot_array_search to recursively search the
	 * array with wildcards.
	 *
	 * @param array $indexes
	 * @param array $array
	 *
	 * @return mixed|null
	 */
	function _array_search_dot(array $indexes, array $array)
	{
		// Grab the current index
		$currentIndex = $indexes
			? array_shift($indexes)
			: null;

		if ((empty($currentIndex)  && intval($currentIndex) !== 0) || (! isset($array[$currentIndex]) && $currentIndex !== '*'))
		{
			return null;
		}

		// Handle Wildcard (*)
		if ($currentIndex === '*')
		{
			// If $array has more than 1 item, we have to loop over each.
			if (is_array($array))
			{
				foreach ($array as $value)
				{
					$answer = _array_search_dot($indexes, $value);

					if ($answer !== null)
					{
						return $answer;
					}
				}

				// Still here after searching all child nodes?
				return null;
			}
		}

		// If this is the last index, make sure to return it now,
		// and not try to recurse through things.
		if (empty($indexes))
		{
			return $array[$currentIndex];
		}

		// Do we need to recursively search this value?
		if (is_array($array[$currentIndex]) && $array[$currentIndex])
		{
			return _array_search_dot($indexes, $array[$currentIndex]);
		}

		// Otherwise we've found our match!
		return $array[$currentIndex];
	}
}

/**
 * This is the first error log if the page is not found
 * 
 * @return json
 */
function no_file_log() {
	// set the headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
	header("Access-Control-Max-Age: 3600");

	// unacceptable query made
    http_response_code(404);

	// print the error message
	print json_encode([
	    "response_code" => 404,
	    "request_request" => $_SERVER['REQUEST_URI'],
	    "route_error" => "Sorry! You are trying to access an invalid route on this server."
	]);

	// exit the query
	die();
}

/** Access denied notice */
function permission_denied() {
	print '<div class="row justify-content-center">
		<div class="col-lg-12 col-md-8 col-sm-12" style="border-radius: 0px">
			<div class="alert alert-danger text-center">
				Sorry! You do not have the required permissions to perform this action.
			</div>
		</div>
	</div>';
}

/** Quick links function */
function quick_links($pref = null) {
	/** Base URL */
	global $baseUrl, $availableQuickLinks;

	/** Default links */
	$default = ["chat", "calendar", "quotes", "policies"];
	
	/** user preferences */
	$pref = is_object($pref) ? (array) $pref : $pref;

	/** if the item is not empty */
	$prefs = stringToArray($pref);
	$prefs = empty($prefs) ? $default : $prefs;
	/** Variable for the string */
	$new_pref = "";
	/** Loop through the user preferences */
	foreach($prefs as $key => $eachPref) {
		// if the key was found
		if(isset($availableQuickLinks[$eachPref])) {
			$data = $availableQuickLinks[$eachPref];
			$new_pref .= "<a href=\"{$baseUrl}{$data["href"]}\"><i {$data["favicon"]}></i><p>{$data["label"]}</p></a>";
		}
	}

	/** Return the text */
	return $new_pref;
}