<?php
set_include_path(__DIR__ . '/../models');
require_once 'dictionary.php';

$dictionary = new dictionary();

// $entries = $dictionary->test_parsing();
// print_r($entries);

$count = $dictionary->load_dictionary();
echo $count;
