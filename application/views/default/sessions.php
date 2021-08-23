<?php
// $session->clientId = "MSGH000004";
// $session->initialAccount_Created = "Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk";
// print_r($session);
$prefix = "MGH";
$token = "afdafdf";
$preference = (object) [
    "labels" => [
        "staff" => "{$prefix}U",
        "student" => "$prefix",
        "parent" => "{$prefix}P",
        "receipt" => "R{$prefix}"
    ],
    "academics" => [
        "academic_year" => date("Y") . "/" . (date("Y") - 1),
        "academic_term" => "",
        "term_starts" => "",
        "term_ends" => "",
        "next_academic_year" => "",
        "next_academic_term" => "",
        "next_term_starts" => "",
        "next_term_ends" => ""
    ],
    "account" => [
        "package" => "trial",
        "activation_code" => $token,
        "date_created" => date("Y-m-d h:iA"),
        "expiry" => date("Y-m-d h:iA", strtotime("+1 months"))
    ],
    "opening_days" => $myClass->default_opening_days,
];
$package = $myClass->pushQuery("*", "clients_packages", "package='Trial' LIMIT 1");
$package = (array) $package[0];
unset($package["id"]);
$new = array_merge($preference->account, $package);
$preference->account = $new;
print json_encode($preference);