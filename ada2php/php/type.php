<?php
type::$custom_types = require 'custom_types.php';

class type
{
    public static $custom_types;

    /**
     * Stores the type value
     *
     * Note that $this->value is used to get or set the type value via a magic method.
     *
     * @var mixed
     */
    protected $data;

    public $is_constant = false;

    /**
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        // the property $value is not meant to be set, it may only be accessed with magic methods
        // unsets the value property if present
        unset($this->value);

        if (is_null($value)) {
            // there is no value, eg variable declaration, sets the value to null
            $this->data = null;

        } else {
            $this->__set('value', $value);
        }
    }

    /**
     *
     * @param string $name
     */
    public function __get($name)
    {
        if ($name == 'value') {
            return $this->data;
        }

        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new Exception("The property is undefined: $name.");
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
            throw new Exception("The property is invalid: $name.");
        }

        if ($this->is_constant) {
            throw new Exception('A constant value may not be changed.');
        }

        if (is_null($value)) {
            throw new Exception('A null value may not be set.');
        }

        $value = $this->filter_and_validate($value);
        $this->set_value($value);
    }

    public function __toString()
    {
        $string = $this->convert_to_string($this->data);

        return $string;
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
     * @param type $mixed
     * @return string
     */
    public function convert_to_string($mixed)
    {
        $string = print_r($mixed, true);
        // replaces extra spaces and line feeds with a single space
        $string = preg_replace('~\s+~', ' ', $string);

        return $string;
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
            // the value and the object type args are passed, instanciates a temporary type
            $type = self::create_temp_type($value, $arg1, $arg2);

        } else {
            // the value only is passed, instanciates a new type
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
        throw new Exception(__FUNCTION__ .  '() method is unavailable.');
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
            throw new Exception("The class is invalid: $type_name.");
        }

        return new $type_name($value);
    }

    /**
     *
     * @param mixed $value
     */
    public function filter_and_validate($value)
    {
        if (is_object($value) and $value instanceof type) {
            // the value is a type object, sets the value to the type object value
            // this allows to copy (clone) or cast a type object into another type object,
            // or to pass a type object instead of its value
            $value = $value->value;
        }

        $value = $this->filter_value($value);
        $this->validate_value($value);

        return $value;
    }

    /**
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter_value($value)
    {
        return $value;
    }

    /**
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter_numeric($value)
    {
        if (is_string($value)) {
            // removes unserscores from a numeric that is used as a digit separator in ADA
            $value = str_replace('_', '', $value);
        }

        return $value;
    }

    public function get_temp_type_name()
    {
        static $number = 0;

        $temp_type_name = 'temp_type_' . $number++;

        return $temp_type_name;
    }

    /**
     *
     * @param int $value
     * @throws Exception
     */
    public function is_value_in_range($value)
    {
        if (! is_null($this->first) and $value < $this->first) {
            throw new Exception("The value is below the range: $value < $this->first.");
        }

        if (! is_null($this->last) and $value > $this->last) {
            throw new Exception("The value is above the range: $value > $this->last.");
        }
    }

    /**
     *
     * @param string $type_name
     * @throws Exception
     */
    public static function load_type($type_name)
    {
        if (! isset(static::$custom_types[$type_name])) {
            throw new Exception("The custom type is invalid: $type_name.");
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
            throw new Exception("Cannot eval the type class: $type_name.");
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
            throw new Exception("The type already exists: $type_name.");
        }

        $new_type = new static();
        $new_type->create_type($type_name, $arg1, $arg2);
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function set_value($value)
    {
        throw new Exception(__FUNCTION__ .  '() method is unavailable.');
    }

    /**
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public function validate_type_properties($arg1 = null, $arg2 = null)
    {
        throw new Exception(__FUNCTION__ .  '() method is unavailable.');
    }

    /**
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate_value($value)
    {
        return true;
    }
}
