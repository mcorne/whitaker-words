<?php
set_include_path('../php');

require_once 'arrays.php';

$class = arrays::new_type('new_array0', 'integer');
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
$a->k(2, 1)->v = 21;
echo $a->key(2, 1)->v;
echo "\n";
// echo $a->key(3, 3)->value;
echo "\n";
echo $a;
echo "\n";
echo "\n";

$b = arrays::create(
    [ // values
        1              => [4 => 11, 5 => 22, 6 => 33],
        2              => [4 => 44, arrays::OTHERS => 55],
        arrays::OTHERS => [5 => 66, arrays::OTHERS => 77],

    ],
    'integer', // value type
    ['integer', 1, 4], // key type 1
    [4, 6]  // key type 2
);
echo $b;
echo "\n";
echo $b->key(1, 5)->value;
echo "\n";
echo $b->key(2, 5)->value;
echo "\n";
echo $b->key(3, 5)->value;
echo "\n";
echo $b->key(3, 6)->value;
echo "\n";
echo "\n";


$c = arrays::create(
    [ // values
        [11, 22, 33, arrays::KEY => false], //
        [4 => 44, arrays::OTHERS => 55], // arrays::KEY => true
        arrays::OTHERS => [5 => 66, arrays::OTHERS => 77],
        // arrays::KEY => false,

    ],
    'integer', // value type
    ['integer', 1, 4], // key type 1
    [4, 6]  // key type 2
);
echo $c;
echo "\n";
echo $c->key(1, 5)->value;
echo "\n";
echo $c->key(2, 5)->value;
echo "\n";
echo $c->key(3, 5)->value;
echo "\n";
echo $c->key(3, 6)->value;
echo "\n";
echo "\n";

$array = [
    ['11', '22'],
    ['33', '44'],
];
$d = arrays::create($array, 'integer', [5, 6], [5, 6]);
$d = arrays::create($array, 'integer', 'natural', 'natural');
$d = arrays::create($array, null, null, null);
$d = arrays::create($array);
$d = arrays::create($array, 'integer');
echo $d->class;
echo "\n";
echo $d;
echo "\n";
echo $d->key(1, 1)->value;
echo "\n";
echo "\n";

$class = arrays::new_type('new_array', [1, 100], [10, 20]);
echo $class;
echo "\n";
$e = new_array::create([1, 2, 3, 4, 5], [11, 20]);
echo $e->class;
echo "\n";
echo $e;
echo "\n";
echo "\n";


$class = arrays::new_type('new_array1', 'integer', 'integer', 'integer');
$f = new_array1::create(
    [ // values
        1 => [0 => 11, 1 => 22],
        2 => [0 => 33, 1 => 44],
    ],
    [1, 2], // key type 1 range only
    [0, 1]  // key type 2 range only
);
echo $f->class;
echo "\n";
echo $f;
echo "\n";
echo "\n";
