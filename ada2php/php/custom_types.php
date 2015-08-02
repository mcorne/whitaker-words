<?php
return [
    // <sub type> => [<parent> , <min>, <max>]
    'character'   => ['enumeration', range("\x00", "\xFF")],
    'natural'     => ['integer', 0],
    'positive'    => ['integer', 1],
];
