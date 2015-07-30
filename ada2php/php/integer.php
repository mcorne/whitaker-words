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
     * Counts the number of significant bits of a positive integer
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
     * Creates the sub type class
     *
     * @param string $type_name
     * @param int $first
     * @param int $last
     * @return string
     */
    public function create_type_class($type_name, $first = null, $last = null)
    {
        if (is_null($first)) {
            $first = $this->first;
        }

        if (is_null($last)) {
            $last = $this->last;
        }

        $size  = $this->calculate_size_in_bits($first, $last);
        $range = $this->get_range($first, $last);
        $exported_range = var_export($range, true);

        $class = "
            class $type_name extends integer
            {
                protected        \$first = $first;
                protected        \$last  = $last;
                protected static \$range = $exported_range;
                protected        \$size  = $size;
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
     * @param int $first
     * @param int $last
     * @return array
     * @throws Exception
     */
    public function get_range($first, $last)
    {
        if (is_null($first) or is_null($last) or ($last - $first) > self::MAX_RANGE) {
            throw new Exception('The range is too large: > ' . self::MAX_RANGE);
        }

        $range = range($first, $last);

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
     * That is the integer itself.
     * Note that ADA allows for the integer to be out of range.
     *
     * @param int $value
     * @return int
     */
    public static function pos($value)
    {
        return $value;
    }

    /**
     * Returns the previous value of an integer
     *
     * Note that ADA allows for the integer to be out of range.
     *
     * @param int $val
     * @return int
     */
    public static function pred($val)
    {
        return $val - 1;
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
     * Returns the following value of an integer
     *
     * Note that ADA allows for the integer to be out of range.
     *
     * @param int $val
     * @return int
     */
    public static function succ($val)
    {
        return $val + 1;
    }

    /**
     * Returns the integer for a given position
     *
     * That is the integer itself.
     * Note that ADA allows for the integer to be out of range.
     *
     * @param int $pos
     * @return int
     */
    public static function val($pos)
    {
        return $pos;
    }

    /**
     * Validates the integer sub type properties
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
     * Validates the value is an integer within the parent range
     *
     * @param int $value
     */
    public function validate_value($value)
    {
        $this->is_integer_value($value);
        $this->is_value_in_range($value);
    }
}
