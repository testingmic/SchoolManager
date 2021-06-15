<?php 
$wordObj = load_class("dictionary", "controllers");
$word = $_GET["q"] ?? "";

$param = (object) [
    "term" => $word
];
$search = $wordObj->search($param);

echo json_encode($search);
?>