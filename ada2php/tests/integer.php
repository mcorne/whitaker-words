<?php
require_once '../php/integer.php';


integer::sub_type('which_type', 0, 9);
integer::sub_type('variant_type', 0, 9);

$a = which_type::create(1);
$b = which_type::create(3);
$c = integer::create(7, 0, 9);

// $d = which_type::create();
$d = integer::create(null, 0, 12);
$d->value = $a->value + $b->value + $c->value;

echo $d;

exit;

integer::sub_type('which_type', 0, 9);
integer::create(1);
integer::create(1, 0, 9);