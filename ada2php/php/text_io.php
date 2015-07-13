<?php
class text_io
{
    public function end_of_file($file /* file_type */) // boolean
    {
        return feof($file);
    }

    public function get_line($file /* file_type */, &$item /* string */, &$last /* natural */)
    {
        $item = fgets($file);

        if ($item === false) {
            $item = null;
            $last = null;

        } else {
            $item = rtrim($item, "\n");
            $last = strlen($item) - 1;
        }
    }
}