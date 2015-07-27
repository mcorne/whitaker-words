<?php
set_include_path('../php');

require_once 'arrays.php';

arrays::new_type('new_array', 'integer');
arrays::new_type('new_array', 'integer', 'integer');
arrays::new_type('new_array', 'integer', [0, 5]);
arrays::new_type('new_array', 'integer', ['integer', 1, 5]);
arrays::new_type('new_array', ['integer', 10, 50], ['integer', 1, 5]);

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

integer::new_type('new_int');
arrays::new_type('new_array', 'new_int');

$a->key(1, 2)->value = 123;
$a->k(1, 2)->v = 123;
