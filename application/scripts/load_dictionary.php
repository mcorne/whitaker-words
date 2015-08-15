<?php
set_include_path(__DIR__ . '/../models');
require_once 'dictionary.php';

$dictionary = new dictionary();
echo $dictionary->load_dictionary();
// print_r($dictionary->test_parsing());
