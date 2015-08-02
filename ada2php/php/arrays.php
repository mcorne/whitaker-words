<?php
require_once 'type.php';

class arrays extends type
{
    /**
     * The (sub) array key to set to true or false to specify if the (sub) array has keys or not
     */
    const KEY = '__KEY__';

    /**
     * The (sub) array key used for default value(s)
     *
     * This is used in place of ADA "others".
     */
    const OTHERS = '__OTHERS__';

    protected $current_value;
    protected $data = [];
    protected $is_key_set = false;
    protected $key_type_args;
    protected $key_types;
    protected $key_types_count;
    protected $last_key_index;
    protected $value_type;
    protected $value_type_args;

    /**
     * Instanciates the value type and the key types
     *
     * @param array $value
     */
    public function __construct($value = null)
    {
        if (get_class($this) != __CLASS__) {
            $this->create_array_types();
        }

        parent::__construct($value);
    }

    /**
     *
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if ($name != 'value' and $name != 'v') {
            // not getting the "value", returns the property value from the parent
            $value = parent::__get($name);

        } elseif (! $this->is_key_set) {
            // the key is not set, returns the array data
            $value = $this->data;

        } elseif (! is_null($value = $this->current_value)) {
            // the key is set, eg $array->key(123)->value, and the value is not null, returns the value
            $this->is_key_set = false;

        } elseif (! is_null($value = $this->get_default_value($this->data))) {
            // the key is set and has a default value, returns the default value
            $this->is_key_set = false;

        } else {
            throw new Exception('The array value is not defined (null)');
        }

        return $value;
    }

    /**
     *
     * @param array $value
     * @param array $value_type_args
     * @return array
     */
    public function add_array_default_args($value, $value_type_args)
    {
        $dimension = $this->calculate_array_dimension($value);
        $key_type_args = array_fill(0, $dimension, null);
        array_unshift($key_type_args, $value_type_args);
        array_unshift($key_type_args, $value);

        return $key_type_args;
    }

    /**
     *
     * @param array $array
     * @return int
     */
    public function calculate_array_dimension($array)
    {
        $dimension = 0;

        foreach ((array) $array as $value) {
            if (is_array($value)) {
                $dimension = max($dimension, $this->calculate_array_dimension($value));
            }
        }

        return $dimension + 1;
    }

    /**
     *
     * @param array $value
     * @return object
     */
    public static function create($value = null, $arg1 = null)
    {
        if (get_called_class() == __CLASS__) {
            // this is the creation of an array of no defined type, eg arrays::create([1, 2, 3], ...)
            if (func_num_args() <= 2) {
                // the array has no value type or key type defined, eg arrays::create([1, 2, 3])
                // or only the value type defined, eg arrays::create([1, 2, 3], "integer")
                $value_type_args = $arg1;
                $array_args = self::singleton()->add_array_default_args($value, $value_type_args);

            } else {
                // the array has a value type and key type defined, eg arrays::create([1, 2, 3], "integer", "natural")
                $array_args = func_get_args();
            }

        } else {
            // this is the creation of an array of a given type, eg small_int::create([1, 2, 3], ...)
            if (func_num_args() <= 1) {
                // the array has no arguments to refine the value type or key type
                $array_args = func_get_args();

            } else {
                // the array has new key type arguments, eg small_int::create([1, 2, 3], [0, 10])
                // note that value type arguments may not be changed
                $new_key_type_args = func_get_args();
                array_shift($new_key_type_args);
                $array_args = static::singleton()->fix_array_args($value, $new_key_type_args);
            }
        }

        $array = call_user_func_array('parent::create', $array_args);

        return $array;
    }

    public function create_array_types()
    {
        $this->value_type = $this->create_new_temp_type($this->value_type_args);

        foreach ($this->key_type_args as $key_type_args) {
            $this->key_types[] = $this->create_new_temp_type($key_type_args);
        }
    }

    /**
     *
     * @param string $type_name
     * @return string
     */
    public function create_type_class($parent_type_name, $type_name, $value_type_args, $key_types_args)
    {
        $exported_value_type_args = var_export($value_type_args, true);
        $exported_key_type_args   = var_export($key_types_args, true);
        $key_types_count = count($key_types_args);
        $last_key_index  = $key_types_count - 1;

        $class = "
            class $type_name extends $parent_type_name
            {
                protected \$key_type_args   = $exported_key_type_args;
                protected \$key_types_count = $key_types_count;
                protected \$last_key_index  = $last_key_index;
                protected \$value_type_args = $exported_value_type_args;
            }
            ";

        return $class;
    }

    /**
     *
     * @param array $value
     * @param array $new_key_type_args
     * @return array
     */
    public function fix_array_args($value, $new_key_type_args)
    {
        $key_type_args = $this->fix_key_type_args($new_key_type_args);
        array_unshift($key_type_args, $this->value_type_args);
        array_unshift($key_type_args, $value);

        return $key_type_args;
    }

    /**
     *
     * @param array $new_key_type_args
     * @return array
     */
    public function fix_key_type_args($new_key_type_args)
    {
        $fixed_key_type_args = [];

        foreach ($this->key_type_args as $index => $key_type_args) {
            if (isset($new_key_type_args[$index])) {
                list($key_type_name) = $key_type_args;
                $key_type_args = (array) $new_key_type_args[$index];
                call_user_func_array([$this->key_types[$index], 'validate_type_properties'], $key_type_args);
                array_unshift($key_type_args, $key_type_name);
            }

            $fixed_key_type_args[] = $key_type_args;
        }

        return $fixed_key_type_args;
    }

    /**
     * Fixes the type arguments
     *
     * @param array|string $type_args
     * @param string $default_type
     * @return array
     * @throws Exception
     */
    public function fix_type_args($type_args, $default_type)
    {
        if (is_null($type_args)) {
            // the type is not defined, default to "natural"
            $type_args = [$default_type];

        } elseif (is_string($type_args)) {
            // the type is passed as a string, eg "integer"
            // converts the string to an array with no range, eg ["integer"]
            $type_args = [$type_args];

        } elseif (! is_array($type_args)) {
            // the type is not an array, it is invalid
            $type_args = $this->convert_to_string($type_args);
            throw new Exception("The array type args are invalid: $type_args");

        } elseif (is_int($type_args[0])) {
            // the type first argument is an integer, eg [10, 20]
            // defaults to an integer type, eg ["integer", 10, 20]
            array_unshift($type_args, 'integer');
        }

        return $type_args;
    }

    /**
     *
     * @param array $data
     * @param int $key_index
     * @return mixed
     */
    public function get_default_value($data, $key_index = 0)
    {
        $key = $this->key_types[$key_index]->value;

        if ($key_index != $this->last_key_index) {
            $key_index++;

            if (isset($data[$key])) {
                $value = $this->get_default_value($data[$key], $key_index);

                if (! is_null($value)) {
                    return $value;
                }
            }

            if (isset($data[self::OTHERS])) {
                $value = $this->get_default_value($data[self::OTHERS], $key_index);

                if (! is_null($value)) {
                    return $value;
                }
            }

            return null;
        }

        if (isset($data[$key])) {
            $value = $data[$key];

        } elseif (isset($data[self::OTHERS])) {
            $value = $data[self::OTHERS];

        } else {
            $value = null;
        }

        return $value;
    }

    /**
     *
     * @param array $array
     * @return bool
     */
    public function is_array_with_keys($array)
    {
        if (isset($array[self::KEY])) {
            return (bool) $array[self::KEY];
        }

        while (key($array) === self::OTHERS) {
            next($array);
        }

        $is_array_with_keys = key($array) !== 0;

        return $is_array_with_keys;
    }

    /**
     * Alias of key()
     */
    public function k()
    {
        return call_user_func_array([$this, 'key'], func_get_args());
    }

    /**
     *
     * @param mixed $key1 value
     * @param mixed $key2 value etc.
     * @return $this
     * @throws Exception
     */
    public function key()
    {
        $keys_count = func_num_args();

        if ($keys_count != $this->key_types_count) {
            throw new Exception("The number of keys is invalid: $keys_count != $this->key_types_count");
        }

        $this->current_value = &$this->data;

        foreach ($this->key_types as $key_index => $key_type) {
            $key_type->value = func_get_arg($key_index);
            $key = $key_type->value;

            if (! isset($this->current_value[$key])) {
                if ($key_index != $this->last_key_index) {
                    $this->current_value[$key] = [];
                } else {
                    $this->current_value[$key] = null;
                }
            }

            $this->current_value = &$this->current_value[$key];
        }

        $this->is_key_set = true;

        return $this;
    }

    /**
     *
     * @param array $array
     * @param int $key_index
     * @return array
     * @throws Exception
     */
    public function set_data($array, $key_index = 0)
    {
        if (! is_array($array))  {
            throw new Exception("The value is not an array for index: $key_index");
        }

        $data = [];
        $key_type = $this->key_types[$key_index];

        if (! $is_array_with_keys = $this->is_array_with_keys($array)) {
            unset($array[self::KEY]);
        }

        foreach ($array as $key => $value) {
            if ($key !== self::OTHERS) {
                if ($is_array_with_keys) {
                    $key_type->value = $key;
                    $key = $key_type->value;
                } else {
                    $key = $key_type->val_dynamic($key);
                }
            }

            if ($key_index == $this->last_key_index)  {
                $this->value_type->value = $value;
                $data[$key] = $value;

            } else {
                $data[$key] = $this->set_data($value, $key_index + 1);
            }
        }

        return $data;
    }

    /**
     *
     * @param mixed $value
     */
    public function set_value($value)
    {
        if ($this->is_key_set) {
            $this->is_key_set = false;
            $this->value_type->value = $value;
            $this->current_value = $this->value_type->value;

        } else {
            $this->data = $this->set_data($value);
        }
    }

    /**
     *
     * @param mixed $value_type_args
     * @param mixed $key_type_args_1
     * @param mixed $key_type_args_2 etc.
     * @return array
     */
    public function validate_type_properties($value_type_args = null)
    {
        $array_types_args = func_get_args();

        $value_type_args = array_shift($array_types_args);
        $value_type_args = $this->fix_type_args($value_type_args, 'integer');

        do {
            $key_type_args = array_shift($array_types_args);
            $key_types_args[] = $this->fix_type_args($key_type_args, 'natural');
        } while ($array_types_args);

        $type_properties = [$value_type_args, $key_types_args];

        return $type_properties;
    }
}
