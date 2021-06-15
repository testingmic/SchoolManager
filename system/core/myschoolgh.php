<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		MySchoolGH Plus
 * @subpackage	MySchoolGH Plus Super Class
 * @category	Core Functions
 * @author		Analitica Innovare Dev Team
 */

// set the default time zone
date_default_timezone_set('Europe/London');

defined('BASEPATH') OR exit('No direct script access allowed');

$config = require(BASEPATH . 'core/common.php');

load_file(array('constants'=>'config'));

$dbconn = load_class('db', 'core');	

load_file(array('security'=>'core'));

$myschoolgh = $dbconn->get_database();
$config = load_class('config', 'core');
$session = load_class('Session', 'libraries/Session');

load_helpers(
	ARRAY(
		'string_helper',
		'email_helper',
		'url_helper',
		'file_helper',
		'time_helper',
		'upload_helper',
		'modal_helper'
	)
);

global $pos, $config, $session;