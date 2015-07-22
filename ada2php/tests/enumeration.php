<?php
set_include_path('../php');

require_once 'enumeration.php';

enumeration::new_type('day', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);
$a = day::create('mon');
echo $a;
echo "\n";

day::new_type('mid_week', 'tue', 'fri');
$b = day::create('tue');
echo $b;
echo "\n";

exit;
