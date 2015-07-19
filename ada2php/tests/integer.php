<?php
require_once '../php/integer.php';


integer::sub_type('which_type', 0, 9);
integer::sub_type('variant_type', 0, 9);

$a = which_type::create(1);
$b = which_type::create(3);
$c = integer::create(7, 0, 9);

// $d = which_type::create();
$d = integer::create(null, 0, 11);
$d->value = $a->value + $b->value + $c->value;
echo $d;
echo "\n";

which_type::sub_type('which_sub_type', 0, 8);
$e = which_sub_type::create(8);
echo $e;
echo "\n";

$e->value = null;
echo $e;
echo "\n";

exit;

record::type();