<?php
set_include_path(__DIR__ . '/../models');
require_once 'word.php';

$word = new word();
// echo $word->load_words();
print_r($word->test_inflect_entry(4660));
