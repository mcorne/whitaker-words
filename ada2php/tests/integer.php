<?php
require_once '../php/integer.php';

integer::new_type('which_type', 0, 9);
integer::new_type('variant_type', 0, 9);

$a = which_type::create(1);
$b = which_type::create(3);
$c = integer::create(7, 0, 9);

// $d = which_type::create();
$d = integer::create(null, 0, 11);
$d->value = $a->value + $b->value + $c->value;
echo $d;
echo "\n";

which_type::new_type('which_sub_type', 0, 8);
$e = which_sub_type::create(8);
echo $e;
echo "\n";

$e->value = null;
echo $e;
echo "\n";

$f = which_type::create($b);
$f->value = 4;
echo $f;
echo "\n";
echo $b;
echo "\n";
$f->value = $a;
echo $f;
echo "\n";

$method = 'integer::new_type';
call_user_func($method, 'natural', 0);
$g = natural::create(11);
echo $g;
echo "\n";

type::load_type('positive');
$h = positive::create(22);
echo $h;
echo "\n";


$i = positive::constant(33);
echo $i;
echo "\n";
// $i->value = 456;

exit;

record::type();