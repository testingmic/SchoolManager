<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Booking
 * @subpackage	Booking Super Class
 * @category	Core Functions
 * @author		Emmanuel Obeng
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$ini = parse_ini_file("db.ini");

$config['base_url'] = $ini['base_url'] ?? null;
$config['manager_dashboard'] = ($ini['base_url'] ?? null) . 'app/';
$config['rowsperpage'] = 40;
$config['version'] = 'v1';
$config['site_url'] = 'https://www.myschoolgh.com';
$config['site_name'] = 'MySchoolGH';
$config['site_email'] = 'info@myschoolgh.com';
$config['developer'] = 'Emmallex Technologies';
$config['update_folder'] = '/var/www/html/myschool_gh/assets/upgrades/';
$config['site_root'] = "/var/www/html/myschool_gh/";
$config['sionoff'] = 'Off';
$config['index_page'] = 'index.php';
$config['uri_protocol']	= 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['default_view_path'] = 'application/views/default/';
$config['default_assets_path'] = '/default';
$config['thumbnail_path'] = 'assets/images/thumbnail/';
$config['error_views_path'] = 'application/views/errors/';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;
$config['encryption_key'] = 'I99_Obeng_F109';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
*/
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'mysch_session';
$config['sess_expiration'] = 3600;
$config['sess_save_path'] = ROOTPATH . "/application/sessions/";
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 1800;
$config['sess_regenerate_destroy'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
*/
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 3600;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();
/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
*/
$config['proxy_ips'] = '';
