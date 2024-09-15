<?php
// if the user is not loggedin then show the login form
if(!loggedIn()) { require "login.php"; exit(-1); }

// end page if not a support personnel logged in
if(!$isSupport) { invalid_route(); exit; }

// global variables
global $myschoolgh, $myClass;

$counter = $myClass->itemsCount("users", "client_id = 'MSGH00001' AND user_type='student' LIMIT 1");

$myfile = fopen("test.txt", "r") or die("Unable to open file!");

// get the last user id
$last_id = $myClass->lastRowId("users") + 1;

print "Running code at ".date("Y-m-d H:i:s")."<br><br>";

$row = 0;
while(!feof($myfile)) {
  $row++;
  $name = fgets($myfile);
  
  // print "{$row}." . $name."<br>";
}

print "<br>Ended at ".date("Y-m-d H:i:s")."<br><br>";
fclose($myfile);
?>
