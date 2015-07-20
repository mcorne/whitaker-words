<?php
type::$custom_types = require 'custom_types.php';

class type
{
    public static $custom_types;
    protected $data;
    public $is_constant = false;

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

        if ($this->is_constant) {
            throw new Exception('Constant may not be set');
        }

        if (is_null($value)) {
            $this->data = null;
            return;
        }

        if (is_object($value) and $value instanceof type) {
            // the value is a type object, sets the value to the type object value
            // this allows to copy (clone) or cast a type object into another type object
            $value = $value->value;
        }

        $this->validate($value);
        $this->set_value($value);
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
     */
    public static function constant($value)
    {
        $constant = static::create($value);
        $constant->is_constant = true;

        return $constant;
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
            $type = self::create_temp_type($value, $arg1, $arg2);

        } else {
            $type = new static($value);
        }

        return $type;
    }

    /**
     *
     * @param mixed $value
     * @param mixed $arg1
     * @param mixed $arg2
     * @return object
     */
    public static function create_temp_type($value = null, $arg1 = null, $arg2 = null)
    {
        $type = new static();
        $temp_type_name = $type->get_temp_type_name();
        $type->create_type($temp_type_name, $arg1, $arg2);
        $temp_type = new $temp_type_name($value);

        return $temp_type;
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public function create_type($type_name, $arg1 = null, $arg2 = null)
    {
        $this->validate_type_properties($arg1, $arg2);
        $this->load_type_class($type_name, $arg1, $arg2);
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public function create_type_class($type_name, $arg1 = null, $arg2 = null)
    {
        throw new Exception("Type class creation method unavailable for $type_name");
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

    /**
     *
     * @param string $type_name
     * @throws Exception
     */
    public static function load_type($type_name)
    {
        if (! isset(static::$custom_types[$type_name])) {
            throw new Exception("Invalid custom type: $type_name");
        }

        $args = static::$custom_types[$type_name];
        $parent_type_name = $args[0];

        if (! class_exists($parent_type_name)) {
            self::create_custom_type($parent_type_name);
        }

        $args[0] = $type_name;
        call_user_func_array("$parent_type_name::new_type", $args);
    }
    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public function load_type_class($type_name, $arg1 = null, $arg2 = null)
    {
        $class = $this->create_type_class($type_name, $arg1, $arg2);

        if (eval($class) === false) {
            throw new Exception("Cannot eval type class: $type_name");
        }
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public static function new_type($type_name, $arg1 = null, $arg2 = null)
    {
        if (class_exists($type_name, false)) {
            throw new Exception("Type already exists: $type_name");
        }

        $new_type = new static();
        $new_type->create_type($type_name, $arg1, $arg2);
    }

    public function set_value($value)
    {
        throw new Exception('Set value method unavailable');
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
    public function validate_type_properties($arg1 = null, $arg2 = null)
    {
        throw new Exception('Type validation method unavailable');
    }
}
