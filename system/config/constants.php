<?php
// start the file
defined('BASEPATH') OR exit('No direct script access allowed');

$ini = parse_ini_file("db.ini");

// set thee default date/time for the php.ini to use
date_default_timezone_set('UTC');

# set the constants for the database connection
defined('DB_HOST')  OR define('DB_HOST', $ini['hostname'] ?? null);
defined('DB_USER')  OR define('DB_USER', $ini['username'] ?? null);
defined('DB_PASS')  OR define('DB_PASS', $ini['password'] ?? null);
defined('DB_NAME')  OR define('DB_NAME', $ini['database'] ?? null);

defined('TIME_PERIOD')      OR define('TIME_PERIOD', 60);
defined('RANDOM_STRING')    OR define('RANDOM_STRING', 12);
defined('ATTEMPTS_NUMBER')  OR define('ATTEMPTS_NUMBER', 7);

defined('DEFAULT_PASS')     OR define('DEFAULT_PASS', 'Password1');
defined('ADMINISTRATOR')    OR define('ADMINISTRATOR', array(1000, 1001));
defined('DEVELOPER')        OR define('DEVELOPER', array(1001));

defined('SITE_DATE_FORMAT')     OR define('SITE_DATE_FORMAT', 'd M Y H:iA');
defined('SITE_URL')             OR define('SITE_URL', config_item('base_url'));
defined('F_SIZE')               OR define("F_SIZE", "10Mb");
define('ACTIVE_RANGE', "3 months");
define('INACTIVE_RANGE', "6 months");