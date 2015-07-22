<?php
set_include_path('../php');

require_once 'enumeration.php';

enumeration::new_type('day', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);
$a = day::create('tue');
echo $a;
echo "\n";
echo $a->pred($a);
echo "\n";
echo $a->pred('thu');
echo "\n";
echo $a->succ('sat');
echo "\n";

day::new_type('mid_week', 'tue', 'fri');
$b = mid_week::create('tue');
echo $b;
echo "\n";
echo $b->first;
echo "\n";
echo $b->last;
echo "\n";
echo $b->pos($b);
echo "\n";
echo $b->pos('thu');
echo "\n";
echo $b->val(3);
echo "\n";
