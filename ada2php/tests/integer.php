<?php
require_once '../php/integer.php';


integer_sub_type::add('which_type', 0, 9);
integer_sub_type::add('variant_type', 0, 9);

$a = which_type::create(1);
$b = which_type::create(3);
$c = which_type::create();
$c->value = $a->value + $b->value;

echo $c;