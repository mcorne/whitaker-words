<?php
set_include_path('../php');

require_once 'enumeration.php';

class day extends enumeration
{
    public $values = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
    public $indexes = ['mon' => 0, 'tue' => 1, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7];
}
$j = day::create('mon');
echo $j;
echo "\n";

exit;
