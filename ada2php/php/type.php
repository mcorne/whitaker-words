<?php
abstract class type
{
    protected $data;

    // the property $value MUST NOT be added in children

    /**
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->__set('value', $value);
    }

    /**
     *
     * @param string $name
     */
    public function __get($name)
    {
        if ($name != 'value') {
            throw new Exception("Undefined property: $name");
        }

        return $this->data;
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if ($name != 'value') {
            throw new Exception("Invalid property: $name");
        }

        if (is_null($value)) {
            $this->data = null;
        } else {
            $this->validate($value);
            $this->set_value($value);
        }
    }

    public function __toString()
    {
        if (is_null($this->data) or is_scalar($this->data)) {
            return (string) $this->data;
        }

        throw new Exception("Cannot convert non scalar to string"); // actually results in a fatal error
    }

    /**
     *
     * @param mixed $value
     * @param mixed $arg1
     * @param mixed $arg2
     * @return object
     */
    public static function create($value = null, $arg1 = null, $arg2 = null)
    {
        if (func_num_args() > 1) {
            $type = self::create_temp_sub_type($value, $arg1, $arg2);

        } else {
            $type = new static($value);
        }

        return $type;
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public function create_sub_type($type_name, $arg1 = null, $arg2 = null)
    {
        $this->validate_sub_type_properties($arg1, $arg2);
        $this->create_sub_type_class($type_name, $arg1, $arg2);
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public function create_sub_type_class($type_name, $arg1 = null, $arg2 = null)
    {
        throw new Exception("Type creation method unavailable for $type_name");
    }

    public static function create_temp_sub_type($value = null, $arg1 = null, $arg2 = null)
    {
        $type = new static();
        $temp_sub_type_name = $type->get_temp_type_name();
        $type->create_sub_type($temp_sub_type_name, $arg1, $arg2);
        $temp_sub_type = new $temp_sub_type_name($value);

        return $temp_sub_type;
    }

    /**
     *
     * @param string $type_name
     * @param mixed $value
     * @return object
     * @throws Exception
     */
    public static function factory($type_name, $value)
    {
        if (! class_exists($type_name, false)) {
            throw new Exception("Unavailable class: $type_name");
        }

        return new $type_name($value);
    }

    public function get_temp_type_name()
    {
        static $number = 0;

        $temp_type_name = 'temp_type_' . $number++;

        return $temp_type_name;
    }

    abstract public function set_value($value);

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public static function sub_type($type_name, $arg1 = null, $arg2 = null)
    {
        if (class_exists($type_name, false)) {
            throw new Exception("Type already exists: $type_name");
        }

        $sub_type = new static();
        $sub_type->create_sub_type($type_name, $arg1, $arg2);
    }

    public function validate($value)
    {
        return true;
    }

    /**
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public function validate_sub_type_properties($arg1 = null, $arg2 = null)
    {
        throw new Exception('Type validation method unavailable');
    }
}
