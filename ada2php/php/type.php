<?php
type::$custom_types = require 'custom_types.php';

class type
{
    public static $custom_types;
    public static $temp_type_names;

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
        if ($name != 'value' and $name != 'v') {
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
     * @param mixed $arg2 etc.
     * @return object
     */
    public static function create($value = null)
    {
        if (func_num_args() > 1) {
            // the value and the object type args are passed, instanciates a temporary type
            $type = call_user_func_array('static::create_temp_type', func_get_args());

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
     * @param mixed $arg2 etc
     * @return object
     */
    public static function create_temp_type($value = null)
    {
        $type = new static();

        if ($args = func_get_args()) {
            $args[0] = get_called_class();
        } else {
            $args = [get_called_class()];
        }

        if (! $temp_type_name = $type->get_temp_type_name($args)) {
            $temp_type_name = $type->create_temp_type_name($args);

            if ($args = func_get_args()) {
                $args[0] = $temp_type_name;
            } else {
                $args = [$temp_type_name];
            }

            call_user_func_array([$type, 'create_type'], $args);
        }

        $temp_type = new $temp_type_name($value);

        return $temp_type;
    }

    /**
     *
     * @staticvar int $number
     * @param array $args
     * @return string
     */
    public function create_temp_type_name($args)
    {
        static $number = 0;

        $key = $this->get_temp_type_key($args);
        $temp_type_name = 'temp_type_' . $number++;
        static::$temp_type_names[$key] = $temp_type_name;

        return $temp_type_name;
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2 etc
     * @return string
     * @throws Exception
     */
    public function create_type($type_name)
    {
        $args = func_get_args();
        array_shift($args);
        call_user_func_array([$this, 'validate_type_properties'], $args);

        $class = call_user_func_array([$this, 'load_type_class'], func_get_args());

        return $class;
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2 etc
     * @throws Exception
     */
    public function create_type_class($type_name)
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

    /**
     *
     * @param array $args
     * @return string
     */
    public function get_temp_type_name($args)
    {
        $key = $this->get_temp_type_key($args);

        if (isset(static::$temp_type_names[$key])) {
            return static::$temp_type_names[$key];
        }

        return null;
    }

    /**
     *
     * @param array $args
     * @return string
     */
    public function get_temp_type_key($args)
    {
        $key = print_r($args, true);

        return $key;
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
     */
    public function load_custom_type($type_name)
    {
        $args = static::$custom_types[$type_name];
        $parent_type_name = $args[0];

        if (! class_exists($parent_type_name, false)) {
            $this->load_custom_type($parent_type_name);
        }

        $args[0] = $type_name;
        call_user_func_array("$parent_type_name::new_type", $args);
    }

    /**
     *
     * @param string $type_name
     * @throws Exception
     */
    public static function load_type($type_name)
    {
        $type = new self();

        if (! $type->type_exists($type_name)) {
            throw new Exception("The type is invalid: $type_name.");
        }
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2 etc
     * @throws Exception
     */
    public function load_type_class($type_name)
    {
        $class = call_user_func_array([$this, 'create_type_class'], func_get_args());

        if (eval($class) === false) {
            throw new Exception("Cannot eval the type class: $type_name.");
        }

        return $class;
    }

    /**
     *
     * @param array $args
     * @return string
     * @throws Exception
     */
    public function new_temp_type($args)
    {
        if (empty($args[0]) or ! is_string($args[0])) {
            throw new Exception('The parent type name is invalid');
        }

        $parent_type_name = $args[0];

        if (! $temp_type_name = $this->get_temp_type_name($args)) {
            $temp_type_name = $this->create_temp_type_name($args);

            if ($args = func_get_args()) {
                $args[0] = $temp_type_name;
            } else {
                $args = [$temp_type_name];
            }

            call_user_func_array("$parent_type_name::create_type", $args);
        }

        return $temp_type_name;
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2 etc.
     * @return string
     * @throws Exception
     */
    public static function new_type($type_name)
    {
        if (! preg_match('~^[a-z_]\w*$~i', $type_name)) {
            throw new Exception("The type (class) name is invalid: $type_name.");
        }

        if (class_exists($type_name, false)) {
            throw new Exception("The type already exists: $type_name.");
        }

        $new_type = new static();
        $class = call_user_func_array([$new_type, 'create_type'], func_get_args());

        return $class;
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
     * @param type $type_name
     * @return boolean
     */
    public function type_exists($type_name)
    {
        if (! is_string($type_name)) {
            return false;
        }

        if (class_exists($type_name, false)) {
            return true;
        }

        if (isset(static::$custom_types[$type_name])) {
            $this->load_custom_type($type_name);
            return true;
        }

        $filename = __DIR__ . "/$type_name.php";

        if (file_exists($filename)) {
            require_once $filename;
            return true;
        }

        return false;
    }

    /**
     *
     * @param mixed $arg1
     * @param mixed $arg2 etc
     * @throws Exception
     */
    public function validate_type_properties()
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
