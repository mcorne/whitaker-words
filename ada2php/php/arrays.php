<?php
require_once 'type.php';

class arrays extends type
{
    protected $key_type_args;
    protected $value_type_args;

    /**
     *
     * @param string $type_name
     * @return string
     */
    public function create_type_class($type_name)
    {
        $exported_key_type_args   = var_export($this->key_type_args, true);
        $exported_value_type_args = var_export($this->value_type_args, true);

        $class = "
            class $type_name extends arrays{
                protected \$key_type_args   = $exported_key_type_args;
                protected \$value_type_args = $exported_value_type_args;
            }
            ";

        return $class;
    }

    public function get_type_name($type_args) // TODO: remove if unused
    {
        if ($this->type_exists($type_args)) {
            $type_name = $type_args;
        } else {
            $type_args = $this->parse_type_args($type_args);
            $type_name = $this->new_temp_type($type_args);
        }

        return $type_name;
    }

    /**
     *
     * @param array $key_types_args
     * @return array
     */
    public function parse_key_type_args($key_types_args)
    {
        if (! $key_types_args) {
            $key_type_args[] = ['integer'];
            return $key_type_args;
        }

        foreach($key_types_args as &$key_type_args) {
            $key_type_args = $this->parse_type_args($key_type_args);
        }

        return $key_type_args;
    }

    /**
     *
     * @param mixed $type_args
     * @return array
     * @throws Exception
     */
    public function parse_type_args($type_args)
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
     * @param mixed $key_type_args_1
     * @param mixed $key_type_args_2 etc.
     */
    public function validate_type_properties($value_type_args = null)
    {
        $this->value_type_args = $this->parse_value_type_args($value_type_args);

        $key_types_args = func_get_args();
        array_shift($key_types_args);
        $this->key_type_args = $this->parse_key_type_args($key_types_args);
    }

    /**
     *
     * @param mixed $value_type_args
     * @return array
     */
    public function parse_value_type_args($value_type_args)
    {
        if (is_null($value_type_args)) {
            $value_type_args = ['integer'];
        } else {
            $value_type_args = $this->parse_type_args($value_type_args);
        }

        return $value_type_args;
    }
}
