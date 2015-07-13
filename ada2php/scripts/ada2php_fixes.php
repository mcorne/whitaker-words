<?php
return [
    '~ +$~m'                       => '',                       // trims right
    '~\n{3,}~'                     => "\n\n",                   // removes extra linefeeds

    '~ *//\*.+?\*//\n~s'           => '',                       // removes commented ada before a fix, eg //* some text *//
    '~ *///[^\n]+\n~'              => '',                       // removes commented ada before a fix, eg /// some text
    '~-- +(.+?)$~m'                => '// $1',                  // fixes ada comments, eg -- some text

    '~with \w+; use (\w+);$~m'  => "require_once '$1.php';", // eg with TEXT_IO; use TEXT_IO;

    '~package (?:body )?(\w+)(?:_PACKAGE)? is~m'
                                   => "class \$1\n{",           // eg package body STRINGS_PACKAGE is
    '~end \w+;$~m'                 => '}',

    // eg INFLECTIONS_FULL_NAME      : constant STRING := "INFLECTS.LAT";
    '~(\w+) +: constant STRING := "([\w.]+)";~m'
                                   => 'const $1 = "$2";',

    // eg function LOWER_CASE(C : CHARACTER) return CHARACTER is
    '~function (\w+)\((\w+) : (?:in )?([\w.]+)\) return (\w+) is$~m'
                                   => 'public static function $1(\$$2 /* $3 */) // $4',

    // eg function MAX(A, B : in INTEGER) return INTEGER is
    '~function (\w+)\((\w+), (\w+) : (?:in )?([\w.]+)\) return (\w+) is$~m'
                                   => 'public static function $1(\$$2, \$$3 /* $4 */) // $5',
    // fixes a partially fixed function
    // eg function TRIM($SOURCE /* STRING */, $SIDE /* TRIM_END */ = 'BOTH') return STRING is
    '~function (\w+)\((.+?)\) return (\w+) is$~m'
                                   => 'public static function $1($2) // $3',

    // eg procedure GET_NON_COMMENT_LINE(F : in TEXT_IO.FILE_TYPE; S : out STRING; LAST : out INTEGER) is
    '~procedure (\w+)\((\w+) : (?:in )?([\w.]+); (\w+) : out ([\w.]+); (\w+) : out ([\w.]+)\) is$~m'
                                   => 'public static function $1(\$$2 /* $3 */, &\$$4 /* $5 */, &\$$6 /* $7 */)',

    '~begin$~m'                 => '{',

    '~if (.+?) then$~m'            => 'if ($1) {',
    '~else$~m'                     => 'else {',
    '~end if;$~m'                  => '}',

    '~(\w+)\.(\w+)\(~'             => '$1::$2(',
];
