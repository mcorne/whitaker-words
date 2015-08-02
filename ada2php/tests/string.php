<?php
set_include_path('../php');

require_once 'type.php';

type::load_type('string');
$a = string::create(['f', 'o', 't']);
echo $a->class;
echo "\n";
echo $a;
echo "\n";

