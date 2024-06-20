<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Helpers
 * @subpackage	URL Helper Functions
 * @category	Core Functions
 * @author		Emmallex Technologies Dev. Team
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

if ( ! function_exists('site_url'))
{
	/**
	 * Site URL
	 *
	 * Create a local URL based on your basepath. Segments can be passed via the
	 * first parameter either as a string or an array.
	 *
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function site_url($uri = '', $protocol = NULL)
	{
		return site_url($uri, $protocol);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('base_url'))
{
	/**
	 * Base URL
	 *
	 * Create a local URL based on your basepath.
	 * Segments can be passed in as a string or an array, same as site_url
	 * or a URL to a file can be passed in, e.g. to an image file.
	 *
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function base_url($uri = '', $protocol = NULL)
	{	
		return base_url($uri, $protocol);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('current_url'))
{
	/**
	 * Current URL
	 *
	 * Returns the full URL (including segments) of the page where this
	 * function is placed
	 *
	 * @return	string
	 */
	function current_url()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?  "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('uri_string'))
{
	/**
	 * URL String
	 *
	 * Returns the URI segments.
	 *
	 * @return	string
	 */
	function uri_string()
	{
		return uri_string();
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('index_page'))
{
	/**
	 * Index page
	 *
	 * Returns the "index_page" from your config file
	 *
	 * @return	string
	 */
	function index_page()
	{
		//return get_instance()->config->item('index_page');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('anchor'))
{
	/**
	 * Anchor Link
	 *
	 * Creates an anchor based on the local URL.
	 *
	 * @param	string	the URL
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		$site_url = is_array($uri)
			? site_url($uri)
			: (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));

		if ($title === '')
		{
			$title = $site_url;
		}

		if ($attributes !== '')
		{
			$attributes = _stringify_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

function safe_mailto() {
	return [];
}

// ------------------------------------------------------------------------

if ( ! function_exists('auto_link'))
{
	/**
	 * Auto-linker
	 *
	 * Automatically links URL and Email addresses.
	 * Note: There's a bit of extra code here to deal with
	 * URLs or emails that end in a period. We'll strip these
	 * off and add them after the link.
	 *
	 * @param	string	the string
	 * @param	string	the type: email, url, or both
	 * @param	bool	whether to create pop-up links
	 * @return	string
	 */
	function auto_link(string $str, string $type = 'both', bool $popup = false, $class = ""): string
    {
        // Find and replace any URLs.
        if ($type !== 'email' && preg_match_all('#(\w*://|www\.)[^\s()<>;]+\w#i', $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            // Set our target HTML if using popup links.
            $target = ($popup) ? ' target="_blank"' : '';

            // We process the links in reverse order (last -> first) so that
            // the returned string offsets from preg_match_all() are not
            // moved as we add more HTML.
            foreach (array_reverse($matches) as $match) {
                // $match[0] is the matched string/link
                // $match[1] is either a protocol prefix or 'www.'
                //
                // With PREG_OFFSET_CAPTURE, both of the above is an array,
                // where the actual value is held in [0] and its offset at the [1] index.
                $a   = '<a href="' . (strpos($match[1][0], '/') ? '' : 'http://') . $match[0][0] . '"' . $target . '>' . $match[0][0] . '</a>';
                $str = substr_replace($str, $a, $match[0][1], strlen($match[0][0]));
            }
        }

        // Find and replace any emails.
        if ($type !== 'url' && preg_match_all('#([\w\.\-\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+[^[:punct:]\s])#i', $str, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match) {
                if (filter_var($match[0], FILTER_VALIDATE_EMAIL) !== false) {
                    $str = substr_replace($str, safe_mailto($match[0]), $match[1], strlen($match[0]));
                }
            }
        }

        return $str;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('prep_url'))
{
	/**
	 * Prep URL
	 *
	 * Simply adds the http:// part if no scheme is included
	 *
	 * @param	string	the URL
	 * @return	string
	 */
	function prep_url($str = '')
	{
		if ($str === 'http://' OR $str === '')
		{
			return '';
		}

		$url = parse_url($str);

		if ( ! $url OR ! isset($url['scheme']))
		{
			return 'http://'.$str;
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('url_title'))
{
	/**
	 * Create URL Title
	 *
	 * Takes a "title" string as input and creates a
	 * human-friendly URL string with a "separator" string
	 * as the word separator.
	 *
	 * @todo	Remove old 'dash' and 'underscore' usage in 3.1+.
	 * @param	string	$str		Input string
	 * @param	string	$separator	Word separator
	 *			(usually '-' or '_')
	 * @param	bool	$lowercase	Whether to transform the output string to lowercase
	 * @return	string
	 */
	function url_title($str, $separator = '-', $lowercase = FALSE)
	{
		define("UTF8_ENABLED", true);

		if ($separator === 'dash')
		{
			$separator = '-';
		}
		elseif ($separator === 'underscore')
		{
			$separator = '_';
		}

		$q_separator = preg_quote($separator, '#');

		$trans = array(
			'&.+?;'			=> '',
			'[^\w\d _-]'		=> '',
			'\s+'			=> $separator,
			'('.$q_separator.')+'	=> $separator
		);

		$str = strip_tags($str);
		foreach ($trans as $key => $val)
		{
			$str = preg_replace('#'.$key.'#i'.(UTF8_ENABLED ? 'u' : ''), $val, $str);
		}

		if ($lowercase === TRUE)
		{
			$str = strtolower($str);
		}

		return trim(trim($str, $separator));
	}
}

// ------------------------------------------------------------------------

// ------------------------------------------------------------------------

if ( ! function_exists('redirect'))
{
	/** 
	 * redirect 	// call the redirect function to redirect the page 
	 * @param url 	string 	$config->base_url() . 'account/index'
	 * @param refresh	string	refresh:3000 where the value is the milli seconds time 
	 * to redirect the page 
	 *
	 */
	function redirect($uri = '', $refresh = null)
	{
		// set the default use case for refresh		
		$use_refresh = false;
		
		if ( ! preg_match('#^(\w+:)?//#i', $uri))
		{
			$uri = site_url($uri);
		}
		
		if ($refresh) {
			# if the refresh time was set then
			# first explode the value sent by the user
			$refresh = explode('refresh:', $refresh);
			
			#confirm that there is a value at the end of the refresh code
			if(isset($refresh[1])) {
			
				# confirm that its a valid number
				if(preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $refresh[1])) {
					
					# refresh time set to integer 
					$time_to_refresh = (int)$refresh[1];
					
					$use_refresh = true;
				}
			}
		}
		
		// Redirect straight if the refresh time was not set
		if (!$use_refresh)
		{
			# redirect the page for the user using javascript window.location.href
			// die("<script>window.location.href='$uri'</script>");
			exit;
		}
		
		die("<script>setTimeout(function() { window.location.href='$uri'; }, $time_to_refresh);</script>");
		exit;		
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('confirm_url_id')) {
	
	/**
	 * Calls the $SITEURL global variable from the home page
	 *
	 * This function lets us grab the current url in the address bar
	 * and then confirm if a portion has been set.
	 * Then finally compare if the str matches the current position 
	 *
	 * @param	integer	$int
	 * @param 	string	$str null
	 * @return	(bool)
	 */
	 
	function confirm_url_id($int, $str = []) {		
		
		# call the global $SITEURL variable 
		global $SITEURL;
		
		if(preg_match('/^[\-+]?[0-9]+$/', $int)) {			
			# compare the current position to what we currently have 
			# return true / false using the tenary mode of comparison
			# if the $str was not provided
			if(empty($str)) {
				return (isset($SITEURL[$int])) ? true : false;
			} else {
				# convert to an array
				$str = _str_to_array($str);
				
				# run a double comparison if both $int and $str were supplied
				if(isset($SITEURL[$int]) && count($str) > 0) {
					#do the comparison
					if(in_array($SITEURL[$int], $str)) {
						return true;
					}
				}
			}
			
		}
		
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('create_slug')) {
	
	/**
	 * Create slug
	 *
	 * Create a url that meets SEO requirements.
	 *
	 * @param	string	$str	String to check
	 * @param	extension	$ext	Extension to add to the string
	 * @return	string|mixed
	 */
	function create_slug($str, $replace = '-', $ext=''){
		$str = strtolower($str);     
		
		//remove query string     
		if(preg_match("#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i",$str)){         
			$parsed_url = parse_url($str);         
			$str = $parsed_url['host'].' '.$parsed_url['path'];         
			//if want to add scheme eg. http, https than uncomment next line         
			$str = $parsed_url['scheme'].' '.$str;    
		}     
		
		//replace / and . with white space     
		$str = preg_replace("/[\/\.]/", " ", $str);     
		$str = preg_replace("/[^a-z0-9_\s-]/", "", $str);  
		
		//remove multiple dashes or whitespaces     
		$str = preg_replace("/[\s-]+/", " ", $str);
		
		//convert whitespaces and underscore to $replace     
		$str = preg_replace("/[\s_]/", $replace, $str);    
		
		//limit the slug size     
		$str = substr($str, 0, 100);     
		
		//slug is generated     
		return ($ext) ? $str.$ext : $str; 
	}
}

// -------------------------------------------------------------------
if ( ! function_exists('jump_to_main')) {
	
	/**
	 * Jump to the main page
	 * 
	 * If no referer was parsed or the request method is not post then redirect the page
	 * 
	 * @return header
	 */
	function jump_to_main($baseUrl = null) {

		// global variables
		global $_SERVER, $session, $myschoolgh, $defaultUser, $myClass;

		// set the current url in session
		$session->user_current_url = current_url();

		//if the user session has expired
		if(!$session->clientId) {
			$response = (object) [
				"title" => "Session Expired!",
				"html" => session_logout()
			];
			if(!isset($_SERVER["HTTP_REFERER"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
				header("location: {$baseUrl}main");
				exit;
			}
			echo json_encode($response);
			exit;
		}

		// redirect the page to the appropriate one
		if(!isset($_SERVER["HTTP_REFERER"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
			// get the default user information
			header("location: {$baseUrl}main");
			exit;
		}

		// reload the client data
		$myClass->client_session_data($session->clientId, false);

		// get the current url
		$current_url = $session->user_current_url;
		$current_url = str_ireplace([$baseUrl], ["{{APPURL}}"], $current_url);

		// confirm if the user has been suspended
		if(!empty($myClass->defaultClientData)) {
			
			// check if the user has changed the password
			if(!$defaultUser->changed_password) {
				$response = (object) [
					"title" => "Change Default Password!",
					"html" => changed_password("Change Default Password!")
				];
				echo json_encode($response);
				exit;
			}
		
			// set the state
			$state = $myClass->defaultClientData->client_state;

			// if the account has been suspended or expired
			if(in_array($state, ["Suspended", "Expired"])) {
				$response = (object) [
					"title" => "Account {$state}!",
					"html" => access_denied($state)
				];
				echo json_encode($response);
				exit;
			}

			// save the current url and attach to the user information
			$stmt = $myschoolgh->prepare("UPDATE users SET last_visited_page = ? WHERE item_id = ? LIMIT 1");
			return $stmt->execute([$current_url, $session->userId]);
			
		}

	}

}