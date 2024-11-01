<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Helpers
 * @subpackage	String Helper Functions
 * @category	Core Functions
 * @author		Emmallex Technologies Dev. Team
 */

// ------------------------------------------------------------------------

if ( ! function_exists('trim_slashes'))
{
	/**
	 * Trim Slashes
	 *
	 * Removes any leading/trailing slashes from a string:
	 *
	 * /this/that/theother/
	 *
	 * becomes:
	 *
	 * this/that/theother
	 *
	 * @todo	Remove in version 3.1+.
	 * @deprecated	3.0.0	This is just an alias for PHP's native trim()
	 *
	 * @param	string
	 * @return	string
	 */
	function trim_slashes($str)
	{
		return trim($str, '/');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('strip_slashes'))
{
	/**
	 * Strip Slashes
	 *
	 * Removes slashes contained in a string or in an array
	 *
	 * @param	mixed	string or array
	 * @return	mixed	string or array
	 */
	function strip_slashes($str)
	{
		if ( ! is_array($str))
		{
			return stripslashes($str);
		}

		foreach ($str as $key => $val)
		{
			$str[$key] = strip_slashes($val);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('strip_quotes'))
{
	/**
	 * Strip Quotes
	 *
	 * Removes single and double quotes from a string
	 *
	 * @param	string
	 * @return	string
	 */
	function strip_quotes($str)
	{
		return str_replace(array('"', "'"), '', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('quotes_to_entities'))
{
	/**
	 * Quotes to Entities
	 *
	 * Converts single and double quotes to entities
	 *
	 * @param	string
	 * @return	string
	 */
	function quotes_to_entities($str)
	{
		return str_replace(array("\'","\"","'",'"'), array("&#39;","&quot;","&#39;","&quot;"), $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('reduce_double_slashes'))
{
	/**
	 * Reduce Double Slashes
	 *
	 * Converts double slashes in a string to a single slash,
	 * except those found in http://
	 *
	 * http://www.some-site.com//index.php
	 *
	 * becomes:
	 *
	 * http://www.some-site.com/index.php
	 *
	 * @param	string
	 * @return	string
	 */
	function reduce_double_slashes($str)
	{
		return preg_replace('#(^|[^:])//+#', '\\1/', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('reduce_multiples'))
{
	/**
	 * Reduce Multiples
	 *
	 * Reduces multiple instances of a particular character.  Example:
	 *
	 * Fred, Bill,, Joe, Jimmy
	 *
	 * becomes:
	 *
	 * Fred, Bill, Joe, Jimmy
	 *
	 * @param	string
	 * @param	string	the character you wish to reduce
	 * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
	 * @return	string
	 */
	function reduce_multiples($str, $character = ',', $trim = FALSE)
	{
		$str = preg_replace('#'.preg_quote($character, '#').'{2,}#', $character, $str);
		return ($trim === TRUE) ? trim($str, $character) : $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('random_string'))
{
	/**
	 * Create a "Random" String
	 *
	 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
	 * @param	int	number of characters
	 * @return	string
	 */
	function random_string($type = 'alnum', $len = 8)
	{
		switch ($type)
		{
			case 'basic':
				return mt_rand();
			case 'alnum':
			case 'numeric':
			case 'nozero':
			case 'super':
			case 'alpha':
				switch ($type)
				{
					case 'alpha':
						$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum':
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'super':
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.@!)(*^$?><+-_';
						break;
					case 'numeric':
						$pool = '0123456789';
						break;
					case 'nozero':
						$pool = '123456789';
						break;
				}
				return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
			case 'unique': // todo: remove in 3.1+
			case 'md5':
				return md5(uniqid(mt_rand()));
			case 'encrypt': // todo: remove in 3.1+
			case 'sha1':
				return sha1(uniqid(mt_rand(), TRUE));
		}
	}
}


/**
 * Get random bytes
 *
 * @param	int	$length	Output length
 * @return	string
 */
function get_random_bytes($length)
{
	if (empty($length) OR ! ctype_digit((string) $length))
	{
		return FALSE;
	}

	if (function_exists('random_bytes'))
	{
		try
		{
			// The cast is required to avoid TypeError
			return random_bytes((int) $length);
		}
		catch (Exception $e)
		{
			// If random_bytes() can't do the job, we can't either ...
			// There's no point in using fallbacks.
			return FALSE;
		}
	}

	// Unfortunately, none of the following PRNGs is guaranteed to exist ...
	if (is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== FALSE)
	{
		// Try not to waste entropy ...
		is_php('5.4') && stream_set_chunk_size($fp, $length);
		$output = fread($fp, $length);
		fclose($fp);
		if ($output !== FALSE)
		{
			return $output;
		}
	}

	if (function_exists('openssl_random_pseudo_bytes'))
	{
		return openssl_random_pseudo_bytes($length);
	}

	return FALSE;
}

// ------------------------------------------------------------------------

if ( ! function_exists('increment_string'))
{
	/**
	 * Add's _1 to a string or increment the ending number to allow _2, _3, etc
	 *
	 * @param	string	required
	 * @param	string	What should the duplicate number be appended with
	 * @param	string	Which number should be used for the first dupe increment
	 * @return	string
	 */
	function increment_string($str, $separator = '_', $first = 1)
	{
		preg_match('/(.+)'.preg_quote($separator, '/').'([0-9]+)$/', $str, $match);
		return isset($match[2]) ? $match[1].$separator.($match[2] + 1) : $str.$separator.$first;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('alternator'))
{
	/**
	 * Alternator
	 *
	 * Allows strings to be alternated. See docs...
	 *
	 * @param	string (as many parameters as needed)
	 * @return	string
	 */
	function alternator()
	{
		static $i;

		if (func_num_args() === 0)
		{
			$i = 0;
			return '';
		}

		$args = func_get_args();
		return $args[($i++ % count($args))];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('repeater'))
{
	/**
	 * Repeater function
	 *
	 * @todo	Remove in version 3.1+.
	 * @deprecated	3.0.0	This is just an alias for PHP's native str_repeat()
	 *
	 * @param	string	$data	String to repeat
	 * @param	int	$num	Number of repeats
	 * @return	string
	 */
	function repeater($data, $num = 1)
	{
		return ($num > 0) ? str_repeat($data, $num) : '';
	}
}

/**
 * Character Limiter
 *
 * Limits the string based on the character count.  Preserves complete words
 * so the character count may not be exactly as specified.
 *
 * @param string $endChar the end character. Usually an ellipsis
 */
function character_limiter($str = null, int $n = 500, string $endChar = '&#8230;'): string
{
	if( empty($str) ) {
		return "";
	}
	if (mb_strlen($str) < $n) {
		return $str;
	}

	// a bit complicated, but faster than preg_replace with \s+
	$str = preg_replace('/ {2,}/', ' ', str_replace(["\r", "\n", "\t", "\x0B", "\x0C"], ' ', $str));

	if (mb_strlen($str) <= $n) {
		return $str;
	}

	$out = '';

	foreach (explode(' ', trim($str)) as $val) {
		$out .= $val . ' ';
		if (mb_strlen($out) >= $n) {
			$out = trim($out);
			break;
		}
	}

	return (mb_strlen($out) === mb_strlen($str)) ? $out : $out . $endChar;
}

/**
 * Limit words
 *
 * Splits the words and then using array_splice it reduces the number of words.
 *
 * @param	string	$str	String to check
 * @param	int	$word_limit	Number to limit the words to 
 * @return	text
 */
function limit_words($str, $word_limit = 10, $exempt = []) {
	
	if(is_array($str)) {
		return $str;
	}
	
	$words = strip_tags($str, $exempt);
	
	$words = explode(" ", $words);
	
	return implode(" ", array_splice($words, 0, $word_limit));
}

/**
 * Minimum Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function min_length($str, $val) {
	if ( ! is_numeric($val))
	{
		return FALSE;
	}

	return ($val <= mb_strlen($str));
}

// --------------------------------------------------------------------

/**
 * Max Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function max_length($str, $val) {
	if ( ! is_numeric($val))
	{
		return FALSE;
	}

	return ($val >= mb_strlen($str));
}

// --------------------------------------------------------------------

/**
 * Validate Contact Number
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function valid_contact($str) {
	return (bool) preg_match("/^[0-9+]+$/", $str);
}

function isvalid_date($str) {
	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $str)) {
	    return true;
	} else {
	    return false;
	}
}

function amount_to_words($number) {

	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = [
		0                   	=> 'Zero',
		1                   	=> 'One',
		2                   	=> 'Two',
		3                   	=> 'Three',
		4                   	=> 'Four',
		5                   	=> 'Five',
		6                   	=> 'Six',
		7                   	=> 'Seven',
		8                   	=> 'Eight',
		9                   	=> 'Nine',
		10                  	=> 'Ten',
		11                  	=> 'Eleven',
		12                  	=> 'Twelve',
		13                  	=> 'Thirteen',
		14                  	=> 'Fourteen',
		15                  	=> 'Fifteen',
		16                  	=> 'Sixteen',
		17                  	=> 'Seventeen',
		18                  	=> 'Eighteen',
		19                  	=> 'Nineteen',
		20                  	=> 'Twenty',
		30                  	=> 'Thirty',
		40                  	=> 'Fourty',
		50                  	=> 'Fifty',
		60                  	=> 'Sixty',
		70                  	=> 'Seventy',
		80                  	=> 'Eighty',
		90                  	=> 'Ninety',
		100                 	=> 'Hundred',
		1000                	=> 'Thousand',
		1000000             	=> 'Million',
		1000000000          	=> 'Billion',
		1000000000000       	=> 'Trillion',
		1000000000000000    	=> 'Quadrillion',
		1000000000000000000 	=> 'Quintillion'
	];

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		return 'This function only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX;
		return false;
	}

	if ($number < 0) {
		return $negative . amount_to_words(abs($number));
	}

	$string = null;
	$fraction = null;

	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . amount_to_words($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = amount_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= amount_to_words($remainder);
			}
			break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = isset($dictionary[$number]) ? ucfirst($dictionary[$number]) : null;
		}
		$string .= implode(' ', $words);
	}

	return $string;

}