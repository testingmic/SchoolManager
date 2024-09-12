<?php
// start the file
defined('BASEPATH') OR exit('No direct script access allowed');

$ini = parse_ini_file("db.ini");

// set thee default date/time for the php.ini to use
date_default_timezone_set('Europe/London');

# set the constants for the database connection
defined('DB_HOST')  OR define('DB_HOST', $ini['hostname'] ?? null);
defined('DB_USER')  OR define('DB_USER', $ini['username'] ?? null);
defined('DB_PASS')  OR define('DB_PASS', $ini['password'] ?? null);
defined('DB_NAME')  OR define('DB_NAME', $ini['database'] ?? null);

define('TIME_PERIOD', 60);
define('RANDOM_STRING', 12);
define('ATTEMPTS_NUMBER', 7);

define('ADMINISTRATOR', array(1000, 1001));
define('DEVELOPER', array(1001));

defined('SITE_DATE_FORMAT') 		OR define('SITE_DATE_FORMAT', 'd M Y H:iA');
defined('SITE_URL') 				OR define('SITE_URL', config_item('base_url'));
defined('F_SIZE') 					OR define("F_SIZE", "10Mb");
define('ACTIVE_RANGE', "3 months");
define('INACTIVE_RANGE', "6 months");