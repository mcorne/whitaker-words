<?php
class file
{
    public function read_lines($filename)
    {
        if (! $lines = @file($filename)) {
            throw new Exception("Cannot read: $filename");
        }

        return $lines;
    }
}