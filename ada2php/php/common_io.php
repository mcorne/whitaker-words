<?php
class common_io
{
    const append_file = 'a';
    const in_file     = 'r';
    const inout_file  = 'w+';
    const out_file    = 'w';

    public static $filenames;

    public static function close($handle)
    {
        if (! @fclose($handle)) {
            throw new Exception('Cannot close file');
        }
    }

    public static function create(&$handle, $mode = self::out_file, $filename = null)
    {
        if ($filename) {
            if (! $handle = @fopen($filename, $mode)) {
                throw new Exception("Cannot create file $filename");
            }

            self::$filenames[(int) $handle] = $filename;

        } else {
            if (! $handle = tmpfile()) {
                throw new Exception('Cannot create temp file');
            }
        }
    }

    public static function delete($handle)
    {
        self::close($handle);
        $filename = self::name($handle);

        if (! @unlink($filename)) {
            throw new Exception("Cannot delete file $filename");
        }
    }

    public static function is_open($handle)
    {
        $is_open = (bool) @fstat($handle);

        return $is_open;
    }

    public static function name($handle)
    {
        if (! isset(self::$filenames[(int) $handle])) {
            throw new Exception('No such file');
        }

        return self::$filenames[(int) $handle];
    }

    public static function open(&$handle, $mode , $filename)
    {
        if (! file_exists($filename)) {
            throw new Exception("File does not exist $filename");
        }

        self::create($handle, $mode, $filename);
    }
}