<?php
set_include_path(__DIR__ . '/../models');
require_once 'word.php';

$word = new word();

// $inflections = $word->test_inflect_entry(2442);
// print_r($inflections);

$count = $word->load_words();
echo $count;
