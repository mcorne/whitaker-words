<?php
return [
    "~(?<![\\$'])\b([ABCILT]|LAST|LX)\b~" => '$$1',
    '~(?<!function )\b(TRIM|HEAD)\(~'     => 'self::$1(',
];
