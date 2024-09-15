<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * List of sanitize filename strings
 *
 * @var	array
 */
$filename_bad_chars =	array(
	'../', '<!--', '-->', '<', '>',
	"'", '"', '&', '$', '#',
	'{', '}', '[', ']', '=',
	';', '?', '%20', '%22',
	'%3c',		// <
	'%253c',	// <
	'%3e',		// >
	'%0e',		// >
	'%28',		// (
	'%29',		// )
	'%2528',	// (
	'%26',		// &
	'%24',		// $
	'%3f',		// ?
	'%3b',		// ;
	'%3d'		// =
);

/**
 * List of never allowed strings
 *
 * @var	array
 */
$_never_allowed_str =	array(
	'document.cookie' => '[removed]',
	'(document).cookie' => '[removed]',
	'document.write'  => '[removed]',
	'(document).write'  => '[removed]',
	'.parentNode'     => '[removed]',
	'.innerHTML'      => '[removed]',
	'-moz-binding'    => '[removed]',
	'<!--'            => '&lt;!--',
	'-->'             => '--&gt;',
	'<![CDATA['       => '&lt;![CDATA[',
	'<comment>'	  => '&lt;comment&gt;',
	'<%'              => '&lt;&#37;'
);

$_xss_hash = $ip_address = NULL;

$charset = 'UTF-8';
$_csrf_expire =	7200;
$_csrf_token_name =	'ci_csrf_token';
$_csrf_cookie_name =	'ci_csrf_token';

/**
 * List of never allowed regex replacements
 *
 * @var	array
 */
$_never_allowed_regex = array(
	'javascript\s*:',
	'(\(?document\)?|\(?window\)?(\.document)?)\.(location|on\w*)',
	'expression\s*(\(|&\#40;)', // CSS and IE
	'vbscript\s*:', // IE, surprise!
	'wscript\s*:', // IE
	'jscript\s*:', // IE
	'vbs\s*:', // IE
	'Redirect\s+30\d',
	"([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
);

// --------------------------------------------------------------------

/**
 * Is ASCII?
 *
 * Tests if a string is standard 7-bit ASCII or not.
 *
 * @param	string	$str	String to check
 * @return	bool
 */
function is_ascii($str) {
	return (preg_match('/[^\x00-\x7F]/S', $str) === 0);
}

// --------------------------------------------------------------------

if ( ! function_exists('remove_invisible_characters'))
{
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
			$non_displayables[] = '/%7f/i';	// url encoded 127
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}

/**
 * Strip Image Tags
 *
 * @param	string	$str
 * @return	string
 */
function strip_image_tags($str)
{
	return preg_replace(
		array(
			'#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i',
			'#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i'
		),
		'\\2',
		$str
	);
}


// ------------------------------------------------------------------------

if ( ! function_exists('html_escape'))
{
	/**
	 * Returns HTML escaped variable.
	 *
	 * @param	mixed	$var		The input string or array of strings to be escaped.
	 * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
	 * @return	mixed			The escaped string or array of strings as a result.
	 */
	function html_escape($var, $double_encode = TRUE)
	{
		if (empty($var))
		{
			return $var;
		}

		if (is_array($var))
		{
			foreach (array_keys($var) as $key)
			{
				$var[$key] = html_escape($var[$key], $double_encode);
			}

			return $var;
		}

		return htmlspecialchars($var, ENT_QUOTES, config_item('charset'), $double_encode);
	}
}


// ------------------------------------------------------------------------

if ( ! function_exists('_stringify_attributes'))
{
	/**
	 * Stringify attributes for use in HTML tags.
	 *
	 * Helper function used to convert a string, array, or object
	 * of attributes to a string.
	 *
	 * @param	mixed	string, array, object
	 * @param	bool
	 * @return	string
	 */
	function _stringify_attributes($attributes, $js = FALSE)
	{
		$atts = NULL;

		if (empty($attributes))
		{
			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		$attributes = (array) $attributes;

		foreach ($attributes as $key => $val)
		{
			$atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
		}

		return rtrim($atts, ',');
	}
}

// --------------------------------------------------------------------

/**
 * Sanitize Filename
 *
 * @param	string	$str		Input file name
 * @param 	bool	$relative_path	Whether to preserve paths
 * @return	string
 */
function sanitize_filename($str, $relative_path = FALSE)
{
	global $filename_bad_chars;
	
	$bad = $filename_bad_chars;

	if ( ! $relative_path)
	{
		$bad[] = './';
		$bad[] = '/';
	}

	$str = remove_invisible_characters($str, FALSE);

	do
	{
		$old = $str;
		$str = str_replace($bad, '', $str);
	}
	while ($old !== $str);

	return stripslashes($str);
}

/**
 * Do Never Allowed
 *
 * @used-by	Security::xss_clean()
 * @param 	string
 * @return 	string
 */
function _do_never_allowed($str)
{
	global $_never_allowed_str, $_never_allowed_regex;
	
	$str = @str_replace(array_keys($_never_allowed_str), $_never_allowed_str, $str);

	foreach ($_never_allowed_regex as $regex)
	{
		$str = preg_replace('#'.$regex.'#is', '[removed]', $str);
	}

	return @$str;
}


/**
 * Properly strip all HTML tags including script and style
 *
 * This differs from strip_tags() because it removes the contents of
 * the `<script>` and `<style>` tags. E.g. `strip_tags( '<script>something</script>' )`
 * will return 'something'. strip_all_tags will return ''
 *
 * @since 2.9.0
 *
 * @param string $string        String containing HTML tags
 * @param bool   $remove_breaks Optional. Whether to remove left over line breaks and white space chars
 * @return string The processed string.
 */
function strip_all_tags( $string, $remove_breaks = false ) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags( $string );

	if ( $remove_breaks ) {
		$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
	}

	return trim( $string );
}

function _wp_specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false ) {
	$string = (string) $string;

	if ( 0 === strlen( $string ) ) {
		return '';
	}

	// Don't bother if there are no specialchars - saves some processing.
	if ( ! preg_match( '/[&<>"\']/', $string ) ) {
		return $string;
	}

	// Account for the previous behaviour of the function when the $quote_style is not an accepted value.
	if ( empty( $quote_style ) ) {
		$quote_style = ENT_NOQUOTES;
	} elseif ( ! in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) ) {
		$quote_style = ENT_QUOTES;
	}

	$_quote_style = $quote_style;

	if ( 'double' === $quote_style ) {
		$quote_style  = ENT_COMPAT;
		$_quote_style = ENT_COMPAT;
	} elseif ( 'single' === $quote_style ) {
		$quote_style = ENT_NOQUOTES;
	}

	$string = htmlspecialchars( $string, $quote_style, $charset, $double_encode );

	// Back-compat.
	if ( 'single' === $_quote_style ) {
		$string = str_replace( "'", '&#039;', $string );
	}

	return $string;

}

function esc_html( $text ) {
	$safe_text = check_invalid_utf8( $text );
	$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
	
	return $safe_text;
}

/**
 * Callback function used by preg_replace.
 *
 *
 * @param array $matches Populated by matches to preg_replace.
 * @return string The text returned after esc_html if needed.
 */
function pre_kses_less_than_callback( $matches ) {
	if ( false === strpos( $matches[0], '>' ) ) {
		return esc_html( $matches[0] );
	}
	return $matches[0];
}

/**
 * Convert lone less than signs.
 *
 * KSES already converts lone greater than signs.
 *
 *
 * @param string $text Text to be converted.
 * @return string Converted text.
 */
function pre_kses_less_than( $text ) {
	return preg_replace_callback( '%<[^>]*?((?=<)|>|$)%', 'pre_kses_less_than_callback', $text );
}

function check_invalid_utf8( $string, $strip = false ) {
	$string = (string) $string;

	if ( 0 === strlen( $string ) ) {
		return '';
	}

	// Store the site charset as a static to avoid multiple calls to get_option().
	static $is_utf8 = null;
	if ( ! isset( $is_utf8 ) ) {
		$is_utf8 = in_array( 'UTF-8', array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) );
	}
	if ( ! $is_utf8 ) {
		return $string;
	}

	// Check for support for utf8 in the installed PCRE library once and store the result in a static.
	static $utf8_pcre = null;
	if ( ! isset( $utf8_pcre ) ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$utf8_pcre = @preg_match( '/^./u', 'a' );
	}
	// We can't demand utf8 in the PCRE installation, so just return the string in those cases.
	if ( ! $utf8_pcre ) {
		return $string;
	}

	// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- preg_match fails when it encounters invalid UTF8 in $string.
	if ( 1 === @preg_match( '/^./us', $string ) ) {
		return $string;
	}

	// Attempt to strip the bad chars if requested (not recommended).
	if ( $strip && function_exists( 'iconv' ) ) {
		return iconv( 'utf-8', 'utf-8', $string );
	}

	return '';
}

function xss_clean( $str, $keep_newlines = false ) {
	if ( is_object( $str ) || is_array( $str ) ) {
		return $str;
	}

	$str = (string) $str;

	$filtered = check_invalid_utf8( $str );

	if ( strpos( $filtered, '<' ) !== false ) {
		$filtered = pre_kses_less_than( $filtered );
		// This will strip extra whitespace for us.
		$filtered = strip_all_tags( $filtered, false );

		// Use HTML entities in a special case to make sure no later
		// newline stripping stage could lead to a functional tag.
		$filtered = str_replace( "<\n", "&lt;\n", $filtered );
	}

	if ( ! $keep_newlines ) {
		$filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
	}
	$filtered = trim( $filtered );

	$found = false;
	while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
		$filtered = str_replace( $match[0], '', $filtered );
		$found    = true;
	}

	if ( $found ) {
		// Strip out the whitespace that may now exist after removing the octets.
		$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
	}

	return $filtered;
}

function custom_clean($str) {
	
	// Remove Invisible Characters
	// This prevents sandwiching null characters
	// between ascii characters, like Java\0script.
	remove_invisible_characters($str);
	
	// Fix &entity\n;
	$str = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $str);
	$str = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $str);
	
	$str = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $str);
	$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
	
	// Remove any attribute starting with "on" or xmlns
	$str = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $str);
	
	// Remove javascript: and vbscript: protocols
	$str = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $str);
	
	$str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $str);
	
	$str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $str);
	
	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $str);
	
	$str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $str);
	
	$str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $str);
	
	// Remove namespaced elements (we do not need them)
	$str = preg_replace('#</*\w+:\w[^>]*+>#i', '', $str);
	
	do {
		// Remove really unwanted tags
		$old_data = $str;
		
		$str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml|pre)[^>]*+>#i', '', $str);
	} while ($old_data !== $str);
	
	// we are done...
	return $str;
}

/**
 * Set cookie
 *
 * Accepts an arbitrary number of parameters (up to 7) or an associative
 * array in the first parameter containing all the values.
 *
 * @param	string|mixed[]	$name		Cookie name or an array containing parameters
 * @param	string		$value		Cookie value
 * @param	int		$expire		Cookie expiration time in seconds
 * @param	string		$domain		Cookie domain (e.g.: '.yourdomain.com')
 * @param	string		$path		Cookie path (default: '/')
 * @param	string		$prefix		Cookie name prefix
 * @param	bool		$secure		Whether to only transfer cookies via SSL
 * @param	bool		$httponly	Whether to only makes the cookie accessible via HTTP (no javascript)
 * @return	void
 */
function set_cookie($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = NULL, $httponly = NULL)
{
	if (is_array($name))
	{
		// always leave 'name' in last place, as the loop will break otherwise, due to $$item
		foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name') as $item)
		{
			if (isset($name[$item]))
			{
				$$item = $name[$item];
			}
		}
	}

	if ($prefix === '' && config_item('cookie_prefix') !== '')
	{
		$prefix = config_item('cookie_prefix');
	}

	if ($domain == '' && config_item('cookie_domain') != '')
	{
		$domain = config_item('cookie_domain');
	}

	if ($path === '/' && config_item('cookie_path') !== '/')
	{
		$path = config_item('cookie_path');
	}

	$secure = ($secure === NULL && config_item('cookie_secure') !== NULL)
		? (bool) config_item('cookie_secure')
		: (bool) $secure;

	$httponly = ($httponly === NULL && config_item('cookie_httponly') !== NULL)
		? (bool) config_item('cookie_httponly')
		: (bool) $httponly;

	if ( ! is_numeric($expire))
	{
		$expire = time() - 86500;
	}
	else
	{
		$expire = ($expire > 0) ? time() + $expire : 0;
	}

	setcookie($prefix.$name, $value, $expire, $path, $domain, $secure, $httponly);
}

/**
* Fetch the IP Address
*
* Determines and validates the visitor's IP address.
*
* @return	string	IP address
*/

function valid_ip($ip) {
	if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
		return false;
	}
	return true;
}

function ip_address() {
	$ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
	foreach ($ip_keys as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
			foreach (explode(',', $_SERVER[$key]) as $ip_address) {
				// trim for safety measures
				$ip_address = trim($ip_address);
				// attempt to validate IP
				if (valid_ip($ip_address)) {
					return $ip_address;
				}
			}
		}
	}
	return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
}

#CREATE A SIMPLE FUNCTION TO RUN A TEST ON USER PASSWORD
function passwordTest($password) {
	if(strlen($password) < 8) {
		return false;
	} elseif(preg_match("/^[a-z]+$/", $password)) {
		return false;
	} elseif(preg_match("/^[A-Z]+$/", $password)) {
		return false;
	} elseif(preg_match("/^[a-zA-Z]+$/", $password)) {
		return false;
	} elseif(preg_match("/^[0-9A-Z]+$/", $password)) {
		return false;
	} elseif(preg_match("/^[0-9a-z]+$/", $password)) {
		return false;
	} elseif(preg_match("/^[0-9]+$/", $password)) {
		return false;
	} else {
		return true;
	}
}

function valid_date($date) { 
    if (false === strtotime($date)) { 
        return false;
    } 
    list($year, $month, $day) = explode('-', $date); 
    return checkdate($month, $day, $year);
}