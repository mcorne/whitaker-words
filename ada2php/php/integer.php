<?php
require_once 'type.php';

class integer extends type
{
    protected $first = -2147483648; // 0x8000 0000, 32 bits
    protected $last  =  2147483647; // 0x7FFF FFFF, 32 bits
    protected $size  = 32;

    /**
     *
     * @param int $first
     * @param int $last
     * @return int
     */
    public function calculate_size($first, $last)
    {
        if (is_null($first) or is_null($last)) {
            $size = $this->size;

        } else {
            $greatest_boundary = max(abs($first), abs($last));
            $size = $this->count_significant_bits($greatest_boundary);
        }

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
        $exported_last  = is_null($last)  ? 'null' : $last;
        $exported_first = is_null($first) ? 'null' : $first;
        $size  = $this->calculate_size($first, $last);

        $class = "
            class $type_name extends integer
            {
                protected \$first = $exported_first;
                protected \$last  = $exported_last;
                protected \$size  = $size;
            }
            ";

        return $class;
    }

    /**
     *
     * @param int $value
     * @return int
     */
    public function filter_value($value)
    {
        $value = $this->filter_numeric($value);

        return $value;
    }

    /**
     *
     * @param int $value
     * @throws Exception
     */
    public function is_integer_value($value)
    {
        if (! is_numeric($value) or $value != (int) (float) $value) {
            $value = $this->convert_to_string($value);
            throw new Exception("The value is not an integer: $value");
        }

    }

    /**
     *
     * @param int $value
     */
    public function set_value($value)
    {
        $this->data = (int) (float) $value;
    }

    /**
     *
     * @param int $first
     * @param int $last
     */
    public function validate_type_properties($first = null, $last = null)
    {
        if (! is_null($first)) {
            parent::validate_value($first);
        }

        if (! is_null($last)) {
            parent::validate_value($last);
        }

        if (! is_null($first) and ! is_null($last) and $first > $last) {
            throw new Exception("The first value is greater than the second one: $first > $last.");
        }
    }

    /**
     *
     * @param int $value
     * @throws Exception
     */
    public function validate_value($value)
    {
        $this->is_integer_value($value);
        $this->is_value_in_range($value);
    }
}
