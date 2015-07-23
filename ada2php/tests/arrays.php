<?php
set_include_path('../php');

require_once 'arrays.php';

arrays::new_type('new_array', 'integer');
arrays::new_type('new_array', 'integer', 'integer');
arrays::new_type('new_array', 'integer', [0, 5]);
arrays::new_type('new_array', 'integer', ['integer', 1, 5]);

arrays::create([1, 2, 3, 4, 5]); // to be mapped to the keys

arrays::new_type('new_array', 'integer', 'char');
arrays::new_type('new_array', 'integer', ['char', 'a', 'f']);

arrays::create([1, 2, 3]);