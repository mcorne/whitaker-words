<?php
require_once 'type.php';

class float extends type
{
    /**
     * Creates the integer sub type class
     *
     * @param string $parent_type_name
     * @param string $type_name
     * @param int $first
     * @param int $last
     * @return string
     */
    public function create_type_class($parent_type_name, $type_name, $first, $last)
    {
        $first = is_null($first) ? 'null' : $first;
        $last  = is_null($last)  ? 'null' : $last;

        $class = "
            class $type_name extends $parent_type_name
            {
                protected \$first = $first;
                protected \$last  = $last;
            }
            ";

        return $class;
    }

    /**
     *
     * @param float $value
     * @return int
     */
    public function filter_value($value)
    {
        $value = $this->filter_numeric($value);

        return $value;
    }

    /**
     *
     * @param float $value
     * @throws Exception
     */
    public function is_float_value($value)
    {
        if (! is_numeric($value) or $value != (float) $value) {
            $value = $this->convert_to_string($value);
            throw new Exception("The value is not a float: $value");
        }
    }

    /**
     * Validates the float sub type properties
     *
     * @param float $first
     * @param float $last
     * @return array
     */
    public function validate_type_properties($first = null, $last = null)
    {
        if (! is_null($first)) {
            $first = $this->validate_value($first);
        }

        if (! is_null($last)) {
            $last = $this->validate_value($last);
        }

        if (! is_null($first) and ! is_null($last) and $first > $last) {
            throw new Exception("The first value is greater than the second one: $first > $last.");
        }

        $type_properties = [$first, $last];

        return $type_properties;
    }

    /**
     * Validates the value is a float within the parent type range
     *
     * @param float $value
     * @return float
     */
    public function validate_value($value)
    {
        $this->is_float_value($value);
        $value = (float) $value;
        $this->is_value_in_range($value);

        return $value;
    }
}
