<?php
set_include_path('../php');

require_once 'arrays.php';

arrays::new_type('new_array', 'integer');
arrays::new_type('new_array', 'integer', 'integer');
arrays::new_type('new_array', 'integer', [0, 5]);
arrays::new_type('new_array', 'integer', ['integer', 1, 5]);

$a = arrays::create();

$a = arrays::create([1, 2, 3, 4, 5])    ; // to be mapped to the keys
$a = arrays::create([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5], false); // keys and values


$array = [
    ['a', 'b'],
    ['d', 'e'],
];
$a = arrays::create($array); 


arrays::new_type('new_array', 'integer', 'char');
arrays::new_type('new_array', 'integer', ['char', 'a', 'f']);

arrays::create([1, 2, 3]);