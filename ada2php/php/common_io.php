<?php
class common_io
{
    const append_file = 'a';
    const in_file     = 'r';
    const inout_file  = 'w+';
    const out_file    = 'w';

    public static $open_filenames;

    public function close()
    {

    }

    public static function create(&$file, $mode = self::out_file, $name = null)
    {
        if (! $name) {

        }

        if ($name) {
            if (! $file = @fopen($name, $mode)) {
                throw new Exception("Cannot create file $name");
            }

        } else {
            if (! $file = tmpfile()) {
                throw new Exception('Cannot create temp file');
            }
        }

        self::$open_filenames[(int) $file] = $name;
    }

    public static function get_temp_filename()
    {
        if (! $name = tempnam(sys_get_temp_dir(), 'www')) {
            throw new Exception('Cannot get temp file name');
        }

        return $name;
    }

    public static function open(&$file, $mode , $name)
    {
        if (! file_exists($name)) {
            throw new Exception("File does not exist $name");
        }

        self::create($file, $mode, $name);
    }
}