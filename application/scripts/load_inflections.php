<?php
set_include_path(__DIR__ . '/../models');
require_once 'inflection.php';

$inflection = new inflection();

// $inflections = $inflection->test_parsing();
// print_r($inflections);

$count = $inflection->load_inflections();
echo $count;
