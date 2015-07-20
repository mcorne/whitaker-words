<?php
require_once 'integer.php';

return [
    // <sub type> => [<parent> , <min>, <max>]
    'natural'     => ['integer', 0],
    'positive'    => ['integer', 1],
];
