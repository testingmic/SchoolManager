<?php 
$wordObj = load_class("dictionary", "controllers");
$word = $_GET["q"] ?? "";

$param = (object) [
    "term" => $word
];

// search results
$search = null;

// if the search term is not empty
if(!empty($word)) {
    $search = $wordObj->search($param);
}

echo !empty($search) ? json_encode($search) : null;
?>