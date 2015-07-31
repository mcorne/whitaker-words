<?php
set_include_path('../php');

require_once 'boolean.php';

$a = boolean::create(true);
echo $a;
echo "\n";
echo $a->first;
echo "\n";
echo $a->last;
echo "\n";
echo boolean::pos($a);
echo "\n";
echo boolean::pos(true);
echo "\n";
echo boolean::val(1);
echo "\n";
echo boolean::pred(true);
echo "\n";