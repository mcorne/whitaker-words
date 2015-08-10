<?php
set_include_path(__DIR__ . '/../models');
require_once 'word.php';

$word = new word();

// $inflections = $word->inflect_noun(123, 'ros', 'ros', 1, 1, 'F');
// print_r($inflections);

$count = $word->load_words();
echo $count;