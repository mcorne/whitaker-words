<?php
require_once 'type.php';

class integer extends type
{
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
        if (is_int($value)) {
            return;
        }

        if (! is_string($value) and ! preg_match('~^[+-]?([1-9][0-9]*|0)~', $value)) {
            throw new Exception("Not an integer: $value");
        }
    }
}

/**
 * eg subtype SUB_INT is INTEGER range 12..144;
 */
class integer_sub_type extends integer
{
    protected $max;
    protected $min;

    /**
     *
     * @param string $type_name
     * @param int $min
     * @param int $max
     */
    public static function create_type($type_name, $min = null, $max = null)
    {
        $class[] = "class $type_name extends integer_sub_type";
        $class[] = '{';

        if (! is_null($min)) {
            $class[] = "public \$min = $min;";
        }

        if (! is_null($max)) {
            $class[] = "public \$max = $max;";
        }

        $class[] = '}';

        $class = implode("\n", $class);
        eval($class);
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function validate($value)
    {
        parent::validate($value);

        if (! is_null($this->min) and $value < $this->min) {
            throw new Exception("Integer below range: $value < $this->min");
        }

        if (! is_null($this->max) and $value > $this->max) {
            throw new Exception("Integer above range: $value > $this->max");
        }
    }

    /**
     *
     * @param int $min
     * @param int $max
     */
    public static function validate_type($min = null, $max = null)
    {
        $integer = new integer();

        if (! is_null($min)) {
            $integer->validate($min);
        }

        if (! is_null($max)) {
            $integer->validate($max);
        }
    }
}

/**
 * eg subtype MONTH is NATURAL range 0..12;
 */
class natural extends integer_sub_type
{
    protected $min = 0;
}