<?php
// if the user is not loggedin then show the login form
if(!loggedIn()) { require "login.php"; exit(-1); }
// end page if not a support personnel logged in
if(!$isSupport) { invalid_route(); exit; }
// : set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT");
header("Access-Control-Max-Age: 3600");
// print the session data
echo json_encode($session);
