<?php
class common_io
{
    const append_file = 'a';
    const in_file     = 'r';
    const inout_file  = 'w+';
    const out_file    = 'w';

    public static $filenames;

    public static function close($file)
    {
        if (! @fclose($file)) {
            throw new Exception("Cannot close file $name");
        }
    }

    public static function create(&$file, $mode = self::out_file, $name = null)
    {
        if (! $name and ! $file = tmpfile()) {
            throw new Exception('Cannot create temp file');

        } elseif (! $file = @fopen($name, $mode)) {
            throw new Exception("Cannot create file $name");
        }

        if ($name) {
            self::$filenames[(int) $file] = $name;
        }
    }

    public static function delete($file)
    {
        if (! isset(self::$filenames[(int) $file])) {
            throw new Exception('No such file');
        }

        self::close($file);

        $filename = self::$filenames[(int) $file];

        if (! @unlink($filename)) {
            throw new Exception("Cannot delete file $filename");
        }
    }

    public static function is_open($file)
    {
        $is_open = (bool) @fstat($file);

        return $file;
    }

    public static function open(&$file, $mode , $name)
    {
        if (! file_exists($name)) {
            throw new Exception("File does not exist $name");
        }

        self::create($file, $mode, $name);
    }
}