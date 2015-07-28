<?php
set_include_path('../php');

require_once 'arrays.php';

$class = arrays::new_type('new_array');
$class = arrays::new_type('new_array0', 'integer');
$class = arrays::new_type('new_array1', 'integer', 'integer');
$class = arrays::new_type('new_array2', 'integer', [0, 5]);
$class = arrays::new_type('new_array3', 'integer', ['integer', 1, 5]);
$class = arrays::new_type('new_array4', ['integer', 10, 50], ['integer', 1, 5]);

$class = arrays::new_type('new_array5', 'integer', 'char');
$class = arrays::new_type('new_array6', 'integer', ['char', 'a', 'f']);

type::load_type('integer');
$class = integer::new_type('new_int');
$class = arrays::new_type('new_array7', 'new_int');

$a = new_array::create();
// $a->key(1, 2)->value = 123;

exit;
//


$a = arrays::create([1, 2, 3, 4, 5])    ; // to be mapped to the keys
$a = arrays::create([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5], false); // keys and values


$array = [
    ['a', 'b'],
    ['d', 'e'],
];
$a = arrays::create($array);



$a->k(1, 2)->v = 123;
