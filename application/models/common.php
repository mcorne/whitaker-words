<?php
class common
{
    public $line_number;

    public function __construct()
    {
        $this->flip_properties();
    }

    public function combine_attributes_and_values($attributes, $values)
    {
        $attributes_count = count($attributes);
        $values_count     = count($values);

        if ($attributes_count != $values_count) {
            $message = $this->set_error_message('Attributes and values do not match: %d != %d.', $attributes_count, $values_count);
            throw new Exception($message);
        }

        $attributes = array_keys($attributes);
        $combined = array_combine($attributes, $values);

        return $combined;
    }

    public function flip_properties()
    {
        $properties = get_object_vars($this);

        foreach ($properties as $property => $values) {
            if (preg_match('~_(attributes|type)$~', $property)) {
                $this->$property = array_flip($values);
            }
        }
    }
    
    public function read_lines($filename)
    {
        if (! $lines = @file($filename)) {
            throw new Exception("Cannot read: $filename");
        }

        return $lines;
    }

    /**
     *
     * @param string $format
     * @param string $arg1
     * @param string $arg2 etc.
     * @return string
     */
    public function set_error_message()
    {
        $args = func_get_args();
        $format = "Error line #%d: " . array_shift($args);
        array_unshift($args, $this->line_number);
        $message = vprintf($format, $args);

        return $message;
    }
}