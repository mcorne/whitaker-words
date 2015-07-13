<?php
require_once 'text_io.php';

class strings_package
{
    public $trim_end = ['LEFT', 'RIGHT', 'BOTH'];

    public $null_string = '';

    public static function max($a, $b /* integer */) // integer
    {
        return max($a, $b);
    }

    public static function min($a, $b /* integer */) // integer
    {
        return min($a, $b);
    }

    public static function lower_case($s /* string */) // string
    {
        return strtolower($s);
    }

    public static function upper_case($s /* string */) // string
    {
        return strtoupper($s);
    }

    public static function trim($source /* string */, $side /* trim_end */ = 'BOTH') // string
    {
        // Removes leading and trailing blanks and returns a STRING staring at 1
        // For a string of all blanks as input it returns NULL_STRING
        if ($side == 'LEFT') {
            $t = ltrim($side);
        } elseif ($side == 'RIGHT') {
            $t = rtrim($side);
        } else {
            $t = trim($side);
        }

        return $t;
    }

    public static function head($source /* string */, $count /* natural */, $pad /* character */ = ' ') // string
    {
        // Truncates or fills a string to exactly N in length
        if ($count < strlen($source)) {
            $t = substr($source, 1, $count);
        } else {
            $t = str_pad($source, $count, $pad);
        }
        return $t;
    }

    public static function get_non_comment_line($f /* TEXT_IO.FILE_TYPE */, &$s /* string */, &$last /* integer */)
    {
        // Reads a text file and outs a string that is as much of the
        // first line encountered that is not a comment
        while (! text_io::end_of_file($f)) {
            text_io::get_line($f, $s, $l);
            $s = self::trim($s);
            $s = self::head($s, 250);
            $first2chars = substr($s, 0, 2);

            if ($first2chars == '  ' or $first2chars == '--') {
                continue;
            }

            list($s) = explode('--', $s);
            $last = strlen($s) - 1;

            return;
        }

        $s = self::head('', 250);
        $last = 0;
    }
}
