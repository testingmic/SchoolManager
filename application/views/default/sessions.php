<?php
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT");
header("Access-Control-Max-Age: 3600");

echo json_encode($session);