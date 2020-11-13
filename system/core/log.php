<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		InsuredMedics Plus
 * @subpackage	InsuredMedics Plus Super Class
 * @category	Core Functions
 * @author		Analitica Innovare Dev Team
 */

class Log {

	public function __construct() { }

	public function write_log($level, $msg) { }

	protected function _format_line($level, $date, $message) { }

	protected static function strlen($str) { }

	protected static function substr($str, $start, $length = NULL){ }
}
