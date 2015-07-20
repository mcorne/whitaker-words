<?php
require_once 'type.php';

class integer extends type
{
    protected $max;
    protected $min;

    /**
     *
     * @param string $type_name
     * @param int $min
     * @param int $max
     * @return string
     */
    public function create_type_class($type_name, $min = null, $max = null)
    {
        $max = is_null($max) ? 'null' : $max;
        $min = is_null($min) ? 'null' : $min;

        $class = "
            class $type_name extends integer
            {
                public \$min = $min;
                public \$max = $max;
            }
            ";

        return $class;
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
            $this->validate_integer($value);
            $this->validate_range($value);
        }
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function validate_integer($value)
    {
        if (is_int($value)) {
            return;
        }

        if (! is_string($value) or ! preg_match('~^[+-]?([1-9][0-9]*|0)~', $value)) {
            throw new Exception("Not an integer: $value");
        }
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function validate_range($value)
    {
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
    public function validate_type_properties($min = null, $max = null)
    {
        parent::validate($min);
        parent::validate($max);
    }
}
