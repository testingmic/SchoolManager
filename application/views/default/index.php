<?php
global $functions, $db, $usersClass;

if($usersClass->loggedIn()):
	$token ="";
	//using the switch case to get the right file to display
	if(isset($SITEURL[1])):
		//set a variable for the file
		$file = $SITEURL[1];
		
		if(file_exists(config_item('default_view_path')."{$file}.php"))
			include_once config_item('default_view_path')."{$file}.php";
		else
			include_once config_item('default_view_path')."error.php";
	else:
		include_once config_item('default_view_path')."main.php";
	endif;
else:
	require config_item('default_view_path')."login.php";
endif;