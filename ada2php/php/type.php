<?php
type::$custom_types = require 'custom_types.php';

class type
{
    public static $custom_types;

    /**
     * Stores the type value
     *
     * Note that $this->value is used to get or set the type value via magic methods.
     *
     * @var mixed
     */
    protected $data;

    protected $data_range;

    protected $first;
    public    $is_constant = false;
    protected $last;

    protected $methods_to_overload = [
        'create_type_class',        // create_type_class($type_name, $arg1, $arg2 ...), this method must be defined for all types
        'get_range',                // get_range(), this method is meant to be defined for some types
        'pos_dynamic',              // pos_dynamic($value), this method is meant to be defined for some types
        'pred_dynamic',             // pred_dynamic($value), this method is meant to be defined for some types
        'set_value',                // set_value($value), this method must be defined for all types
        'succ_dynamic',             // succ_dynamic($value), this method is meant to be defined for some types
        'val_dynamic',              // val_dynamic($index), this method is meant to be defined for some types
        'validate_type_properties', // validate_type_properties($arg1, $arg2 ...), this method must be defined for all types
    ];

    protected static $number = 0;

    protected static $singletons;
    protected $size;
    public static $type_classes;

    public function __call($name, $args)
    {
        if (in_array($name, $this->methods_to_overload)) {
            $message = sprintf('The %s::%s() method is unavailable.', get_class($this), $name);

        } else {
            $message = "The object method is invalid: $name.";
        }

        throw new Exception($message);
    }

    /**
     *
     * @param string $name
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, $args)
    {
        if (in_array($name, ['first', 'last', 'range', 'size'])) {
            // instanciates the type, returns the corresponding object property
            // eg week_days::first() will return $week_days->first
            $value = static::singleton()->$name;
            return $value;
        }

        if (in_array($name, ['pos', 'pred', 'succ', 'val'])) {
            // instanciates the type, calls the corresponding object method
            // eg week_days::succ('Mon') will call $week_days->succ_dynamic('Mon')
            $name .= '_dynamic';
            $value = call_user_func_array([static::singleton(), $name], $args);
            return $value;
       }

        throw new Exception("The static method is invalid: $name.");
    }

    /**
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        // unsets properties which may only be accessed via magic methods
        unset($this->class, $this->range, $this->value);

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
        switch ($name) {
            case 'class':
                $type_name = get_class($this);
                $value = isset(self::$type_classes[$type_name]) ? self::$type_classes[$type_name] : null;
                break;

            case 'range':
                if (! isset($this->data_range)) {
                    $this->data_range = $this->get_range();
                }

                $value = $this->data_range;
                break;

            case 'v':
            case 'value':
                $value = $this->data;
                break;

            default:
                if (! property_exists($this, $name)) {
                    throw new Exception("The property is undefined: $name.");
                }

                $value = $this->$name;
        }

        return $value;
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
            $type = static::singleton()->create_temp(func_get_args());

        } else {
            // the value only is passed, instanciates a new type
            $type = new static($value);
        }

        return $type;
    }

    /**
     *
     * @param array $type_args
     * @return string
     * @throws Exception
     */
    public function create_new_temp_type($type_args)
    {
        $parent_type_name = array_shift($type_args);
        $this->load_type_dynamic($parent_type_name);
        $parent_type = new $parent_type_name();

        $temp_type_name = $this->create_temp_type_name();
        $parent_type->create_type($temp_type_name, $type_args);

        $temp_type = new $temp_type_name();

        return $temp_type;
    }

    /**
     *
     * @param mixed $value
     * @param array $type_args
     * @return object
     */
    public function create_temp($type_args)
    {
        $value = array_shift($type_args);
        $temp_type_name = $this->create_temp_type($type_args);
        $temp_type = new $temp_type_name($value);

        return $temp_type;
    }

    /**
     *
     * @param array $type_args
     * @return string
     */
    public function create_temp_type($type_args)
    {
        $temp_type_name = $this->create_temp_type_name();
        $this->create_type($temp_type_name, $type_args);

        return $temp_type_name;
    }

    /**
     *
     * @staticvar int $number
     * @return string
     */
    public function create_temp_type_name()
    {
        $temp_type_name = 'temp_type_' . self::$number++;

        return $temp_type_name;
    }

    /**
     *
     * @param string $type_name
     * @param array $type_args
     * @return string
     */
    public function create_type($type_name, $type_args)
    {
        $type_properties = call_user_func_array([$this, 'validate_type_properties'], $type_args);

        $class_args[] = get_class($this); // this is the parent type to extend
        $class_args[] = $type_name;
        $class_args = array_merge($class_args, $type_properties);

        $type_class = call_user_func_array([$this, 'create_type_class'], $class_args);

        if (eval($type_class) === false) {
            throw new Exception("Cannot eval the type class: $type_name.");
        }

        $type_class = preg_replace('~^ {12}~m', '', $type_class); // removes extra indentations
        self::$type_classes[$type_name] = $type_class;

        return $type_class;
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
        $value = $this->validate_value($value);

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
            // removes unserscores from a numeric that may be used as a digit separator in ADA
            $value = str_replace('_', '', $value);
        }

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
        $type_args = static::$custom_types[$type_name];

        $parent_type_name = $type_args[0];
        $this->load_type_dynamic($parent_type_name);

        $type_args[0] = $type_name;
        call_user_func_array("$parent_type_name::new_type", $type_args);
    }

    /**
     *
     * @param string $type_name
     * @throws Exception
     */
    public static function load_type($type_name)
    {
        self::singleton()->load_type_dynamic($type_name);
    }

    /**
     *
     * @param string $type_name
     * @throws Exception
     */
    public function load_type_dynamic($type_name)
    {
        if (! $this->type_exists($type_name)) {
            throw new Exception("The type is invalid: $type_name.");
        }
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
        if (! is_string($type_name) or ! preg_match('~^[a-z_]\w*$~i', $type_name)) {
            $type_name = static::singleton()->convert_to_string($type_name);
            throw new Exception("The type (class) name is invalid: $type_name.");
        }

        if (class_exists($type_name, false)) {
            throw new Exception("The type already exists: $type_name.");
        }

        $type_args = func_get_args();
        array_shift($type_args);
        $type_class = static::singleton()->create_type($type_name, $type_args);

        return $type_class;
    }

    /**
     *
     * @param mixed $value
     */
    public function set_value($value)
    {
        $this->data = $value;
    }

    public static function singleton()
    {
        $type_name = get_called_class();

        if (! isset(self::$singletons[$type_name])) {
            self::$singletons[$type_name] = new static();
        }

        return self::$singletons[$type_name];
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
     * Validates the type range properties
     *
     * @param mixed $first
     * @param mixed $last
     * @return array
     * @throws Exception
     */
    public function validate_type_range_properties($first = null, $last = null)
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
     *
     * @param mixed $value
     * @return mixed
     */
    public function validate_value($value)
    {
        return $value;
    }
}
