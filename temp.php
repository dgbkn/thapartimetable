<?php

$re = '/[UP][A-Za-z]{2}\d{3}/m';
$str = 'UCS531L/UCS532L/UCS534L/UCS537L/UCS539L/UCS546L/UCS547L/UCS550L/UCS542L/UCS548L/UMC512L';

preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

// Print the entire match result
print_r($matches);
  ?>