<?php
require_once 'type.php';

/**
 * The ADA 32 bit integer type
 *
 * @toto handle mod, eg type Byte is mod 256
 */
class integer extends type
{
    const MAX_RANGE = 100000;

    protected $first = -2147483648; // 0x8000 0000, 32 bits
    protected $last  =  2147483647; // 0x7FFF FFFF, 32 bits
    protected $size  = 32;

    /**
     * Calculates the size of the integer sub type in bits
     *
     * @param int $first
     * @param int $last
     * @return int
     */
    public function calculate_size_in_bits($first, $last)
    {
        $greatest_boundary = max(abs($first), abs($last));
        $size = $this->count_significant_bits($greatest_boundary);

        return $size;
    }

    /**
     * Creates the integer sub type class
     *
     * @param string $parent_type_name
     * @param string $type_name
     * @param int $first
     * @param int $last
     * @return string
     */
    public function create_type_class($parent_type_name, $type_name, $first = null, $last = null)
    {
        if (is_null($first)) {
            $first = $this->first;
        }

        if (is_null($last)) {
            $last = $this->last;
        }

        $size  = $this->calculate_size_in_bits($first, $last);

        $class = "
            class $type_name extends $parent_type_name
            {
                protected \$first = $first;
                protected \$last  = $last;
                protected \$size  = $size;
            }
            ";

        return $class;
    }

    /**
     * Filters the integer
     *
     * Note that ADA allows the underscore character as thousand separator etc.
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
     * @return array
     * @throws Exception
     */
    public function get_range()
    {
        if (is_null($this->first) or is_null($this->last) or ($this->last - $this->first) > self::MAX_RANGE) {
            throw new Exception('The range is too large: > ' . self::MAX_RANGE);
        }

        $range = range($this->first, $this->last);

        return $range;
    }

    /**
     * Validates the value is an integer
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
     * Returns the position of an integer
     *
     * Note that ADA actually returns the integer itself.
     *
     * @param int $value
     * @return int
     */
    public function pos_dynamic($value)
    {
        $pos = $this->filter_and_validate($value) - $this->first;

        return $pos;
    }

    /**
     * Returns the previous value of an integer
     *
     * @param int $value
     * @return int
     */
    public function pred_dynamic($value)
    {
        $value = $this->filter_and_validate($value);
        $value--;
        $value = $this->validate_value($value);

        return $value - 1;
    }

    /**
     * Returns the following value of an integer
     *
     * @param int $value
     * @return int
     */
    public function succ_dynamic($value)
    {
        $value = $this->filter_and_validate($value);
        $value++;
        $value = $this->validate_value($value);

        return $value;
    }

    /**
     * Returns the integer for a given position or index
     *
     * Note that ADA actually returns the integer itself.
     *
     * @param int $index
     * @return int
     */
    public function val_dynamic($index)
    {
        if (! is_int($index)) {
            $index = $this->convert_to_string($index);
            throw new Exception("The index is invalid: $index");
        }

        $value = $index + $this->first;
        $this->is_value_in_range($value);

        return $value;
    }

    /**
     * Validates the integer sub type properties
     *
     * @param int $first
     * @param int $last
     * @return array
     */
    public function validate_type_properties($first = null, $last = null)
    {
        $type_properties = $this->validate_type_range_properties($first, $last);

        return $type_properties;
    }

    /**
     * Validates the value is an integer within the parent type range
     *
     * @param int $value
     * @return int
     */
    public function validate_value($value)
    {
        $this->is_integer_value($value);
        $value = (int) (float) $value;
        $this->is_value_in_range($value);

        return $value;
    }
}
