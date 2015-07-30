<?php
require_once 'type.php';

class float extends type
{
    protected $size = 32;

    /**
     *
     * @param string $type_name
     * @param float $first
     * @param float $last
     * @return string
     */
    public function create_type_class($type_name, $first = null, $last = null)
    {
        $last  = is_null($last)  ? 'null' : $last;
        $first = is_null($first) ? 'null' : $first;

        $class = "
            class $type_name extends float
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
     *
     * @param float $value
     */
    public function set_value($value)
    {
        $this->data = (float) $value;
    }

    /**
     *
     * @param float $first
     * @param float $last
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
        $this->is_float_value($value);
        $this->is_value_in_range($value);
    }
}
