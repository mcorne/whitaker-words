<?php
set_include_path(__DIR__ . '/../models');
require_once 'search.php';

$search = new search();
// $search->load_search();
// print_r($search->search_word('illud'));
print_r($search->search_word('arfecisti'));
