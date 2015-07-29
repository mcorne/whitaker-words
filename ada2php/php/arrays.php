<?php
require_once 'type.php';

class arrays extends type
{
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
        if ($name == 'value' and $this->is_key_set) {
            $this->is_key_set = false;
            $value = $this->current_value;

            if (is_null($value)) {
                $value = $this->get_default_value();
            }

            return $value;
        }

        parent::__get($name);
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
    public function create_type_class($type_name)
    {
        $exported_key_type_args   = var_export($this->key_type_args, true);
        $exported_value_type_args = var_export($this->value_type_args, true);
        $key_types_count = count($this->key_type_args);
        $last_key_index  = $key_types_count - 1;

        $class = "
            class $type_name extends arrays{
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
     * @param array $key_types_args
     * @return array
     */
    public function fix_key_type_args($key_types_args)
    {
        if (! $key_types_args) {
            $key_type_args[] = ['integer'];
            return $key_type_args;
        }

        $fixed = [];

        foreach ($key_types_args as $key_type_args) {
            $fixed[] = $this->fix_type_args($key_type_args);
        }

        return $fixed;
    }

    /**
     *
     * @param mixed $type_args
     * @return array
     * @throws Exception
     */
    public function fix_type_args($type_args)
    {
        if (is_string($type_args)) {
            $type_args = [$type_args];

        } elseif (! is_array($type_args)) {
            $type_args = $this->convert_to_string($type_args);
            throw new Exception("The array type args are invalid: $type_args");

        } elseif (! is_string($type_args[0])) {
            array_unshift($type_args, 'integer');
        }

        return $type_args;
    }

    /**
     *
     * @param mixed $value_type_args
     * @return array
     */
    public function fix_value_type_args($value_type_args)
    {
        if (is_null($value_type_args)) {
            $value_type_args = ['integer'];
        } else {
            $value_type_args = $this->fix_type_args($value_type_args);
        }

        return $value_type_args;
    }

    /**
     *
     * @return mixed
     * @throws Exception
     */
    public function get_default_value()
    {
        $data = $this->data;

        foreach ($this->key_types as $index => $key_type) {
            $key = $key_type->value;

            if (isset($data[$key]) and ($index == $this->last_key_index or ! empty($data[$key]))) {
                $data = $data[$key];

            } elseif (isset($data[self::OTHERS])) {
                $data = $data[self::OTHERS];

            } else {
                throw new Exception('The array value has no default value');
            }
        }

        return $data;
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

        foreach ($this->key_types as $index => $key_type) {
            $key_type->value = func_get_arg($index);
            $key = $key_type->value;

            if (! isset($this->current_value[$key])) {
                if ($index != $this->last_key_index) {
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

        foreach ($array as $key => $value) {
            if ($key != self::OTHERS) {
                $key_type->value = $key;
                $key = $key_type->value;
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
     */
    public function validate_type_properties($value_type_args = null)
    {
        $this->value_type_args = $this->fix_value_type_args($value_type_args);

        $key_types_args = func_get_args();
        array_shift($key_types_args);
        $this->key_type_args = $this->fix_key_type_args($key_types_args);
    }
}
