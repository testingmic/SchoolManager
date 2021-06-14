<?php 
$fees = [
    "dinning" => 200,
    "transport" => 150,
    "tuition" => 500,
    "ict_dues" => 75,
    "project" => 170
];

$fees_list = [];
$paying = 1050;
$init_paying = $paying;

foreach($fees as $key => $value) {
    if($paying === 0) {
        break;
    }
    if(($value < $paying) || ($value === $paying)) {
        $paying = $paying - $value;
        $fees_list[$key] = 0;
    } elseif($value > $paying) {
        $n_value = $value - $paying;
        $fees_list[$key] = $n_value;
        $paying = 0;
    }
}
$fees_list = array_merge($fees, $fees_list);

print_r([
    "fees" => $fees,
    "fees_list" => $fees_list,
    "owing" => array_sum($fees),
    "paid" => $init_paying,
    "arrears" => array_sum($fees_list)
]);
?>