<?php
require_once 'type.php';

class integer extends type
{
    protected $first = -2147483648; // 0x8000 0000, 32 bits
    protected $last  =  2147483647; // 0x7FFF FFFF, 32 bits

    /**
     *
     * @param int $first
     * @param int $last
     * @return int
     */
    public function calculate_size($first, $last)
    {
        $greatest_boundary = max(abs($first), abs($last));
        $size = $this->count_significant_bits($greatest_boundary);

        return $size;
    }

    /**
     *
     * @param int $positive
     * @return int
     * @todo fix to return the same size as ADA, eg 258 needs 9 bits vs 10 in ADA
     */
    public function count_significant_bits($positive)
    {
        $binary = decbin($positive);
        $left_trimmed = ltrim($binary, '0');
        $significant_bit_count = strlen($left_trimmed);

        return $significant_bit_count;
    }

    /**
     *
     * @param string $type_name
     * @param int $first
     * @param int $last
     * @return string
     */
    public function create_type_class($type_name, $first = null, $last = null)
    {
        $last = is_null($last) ? 'null' : $last;
        $first = is_null($first) ? 'null' : $first;
        $size = $this->calculate_size($first, $last);

        $class = "
            class $type_name extends integer
            {
                protected \$first = $first;
                protected \$last  = $last;
                protected \$size  = $size;
            }
            ";

        return $class;
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function is_integer_value($value)
    {
        if (is_int($value)) {
            return;
        }

        if (! is_string($value) or ! preg_match('~^[+-]?([1-9][0-9]*|0)~', $value)) {
            throw new Exception("The value is not an integer: $value.");
        }
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function is_value_in_range($value)
    {
        if (! is_null($this->first) and $value < $this->first) {
            throw new Exception("The integer value is below the range: $value < $this->first.");
        }

        if (! is_null($this->last) and $value > $this->last) {
            throw new Exception("The integer value is above the range: $value > $this->last.");
        }
    }

    /**
     *
     * @param mixed $value
     */
    public function set_value($value)
    {
        $this->data = (int) $value;
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function validate($value)
    {
        if (! is_null($value)) {
            $this->is_integer_value($value);
            $this->is_value_in_range($value);
        }
    }

    /**
     *
     * @param int $first
     * @param int $last
     */
    public function validate_type_properties($first = null, $last = null)
    {
        parent::validate($first);
        parent::validate($last);

        if (! is_null($first) and ! is_null($last) and $first > $last) {
            throw new Exception("The first integer is greater than the second integer: $first > $last.");
        }
    }
}
