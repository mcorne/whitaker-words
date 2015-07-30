<?php
set_include_path('../php');

require_once 'integer.php';

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

// $e->value = null;

$f = which_type::create($b);
$f->v = 4;
echo $f;
echo "\n";
echo $b;
echo "\n";
$f->v = $a;
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

$i = positive::constant('3_322');
echo $i;
echo "\n";
echo $i->is_constant;
echo "\n";
// $i->value = 456;

integer::new_type('bug_range', -13, 12);
$j = bug_range::create(0);
echo $j->first;
echo "\n";
echo $j->last;
echo "\n";
echo $j->size;
echo "\n";
echo bug_range::pos(10);
echo "\n";
echo bug_range::val(10);
echo "\n";
echo bug_range::pred(10);
echo "\n";
echo bug_range::succ(10);
echo "\n";
print_r(bug_range::range());
echo "\n";

$class = integer::new_type('integer2');
print_r((array) $class);
echo "\n";
$class = integer::new_type('integer3', null, 22);
print_r((array) $class);
echo "\n";
$class = integer::new_type('integer4', 11);
print_r((array) $class);
echo "\n";


// TODO: make first and last static, like pos, val etc.