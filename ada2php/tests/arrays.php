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

$class = arrays::new_type('new_array8', 'integer', 'integer', 'integer');
$a = new_array8::create();
$a->key(1, 2);
$a->value = 12;
$a->key(2, 1)->value = 21;
echo $a->key(2, 1)->value;
echo "\n";
echo $a->key(3, 3)->value;
echo "\n";
echo $a;
echo "\n";
exit;

//

// must define ranges for keys if undefined
$a = new_array::create([1, 2, 3, 4, 5], [0, 4]);

// as sub type
$a = new_array1::create(
    [ // values
        1 => [0 => 11, 1 => 22],
        2 => [0 => 33, 1 => 44],
    ],
    [1, 2], // key type 1 range only
    [0, 1]  // key type 2 range only
);

// as brand new type
$a = arrays::create(
    [ // values
        1 => [0 => 11, 1 => 22],
        2 => [0 => 33, 1 => 44],
    ],
    'integer', // value type
    ['integer', 1, 2], // key type 1
    [0, 1]  // key type 2
);

// default interger with subtypes gather from input
$array = [
    ['11', '22'],
    ['33', '33'],
];
$a = arrays::create($array);



$a->k(1, 2)->v = 123;
