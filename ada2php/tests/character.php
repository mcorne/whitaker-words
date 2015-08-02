<?php
set_include_path('../php');

require_once 'type.php';

type::load_type('character');
$a = character::create('B');
echo $a;
echo "\n";
echo $a->first;
echo "\n";
echo $a->last;
echo "\n";
print_r($a->range);
echo "\n";
echo $a->size;
echo "\n";
echo $a->pos_dynamic('A');
echo "\n";
echo $a->pred_dynamic($a);
echo "\n";
echo $a->pred_dynamic('D');
echo "\n";
echo $a->succ_dynamic('D');
echo "\n";
echo $a->val_dynamic(65);
echo "\n";
echo "\n";

type::load_type('character');
$class = character::new_type('character2', 'a', 'f');
echo $class;
echo "\n";
echo "\n";


exit;


require_once 'enumeration.php';
$a = enumeration::new_type('character2', range("\x00", "\xFF"));
echo $a;
echo "\n";
echo "\n";
