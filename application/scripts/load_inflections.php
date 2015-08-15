<?php
set_include_path(__DIR__ . '/../models');
require_once 'inflection.php';

$inflection = new inflection();
echo $inflection->load_inflections();
// print_r($inflection->test_parsing());
