<?php
set_include_path(__DIR__ . '/../models');
require_once 'inflection.php';
$inflection = new inflection();
$inflections = $inflection->load_inflections();
print_r($inflections);
