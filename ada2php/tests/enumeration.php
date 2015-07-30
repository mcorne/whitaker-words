<?php
set_include_path('../php');

require_once 'enumeration.php';
require_once 'boolean.php';

enumeration::new_type('day', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);
$a = day::create('tue');
echo $a;
echo "\n";
echo day::pred($a);
echo "\n";
echo day::pred('thu');
echo "\n";
echo day::succ('sat');
echo "\n";

day::new_type('mid_week', 'tue', 'fri');
$b = mid_week::create('tue');
echo $b;
echo "\n";
echo mid_week::first();
echo "\n";
echo mid_week::last();
echo "\n";
echo mid_week::pos($b);
echo "\n";
echo mid_week::pos('thu');
echo "\n";
echo mid_week::val(3);
echo "\n";
print_r(mid_week::range());
echo "\n";

$c = boolean::create(true);
echo $c;
echo "\n";
echo $c->first;
echo "\n";
echo $c->last;
echo "\n";
echo boolean::pos($c);
echo "\n";
echo boolean::pos(true);
echo "\n";
echo boolean::val(1);
echo "\n";
echo boolean::pred(true);
echo "\n";