<?php
require_once 'type.php';

class arrays extends type
{
    protected $key_type_names;
    protected $value_type_name;

    public function get_key_or_value_type()
    {
        if ($this->type_exists($value_type_args)) {
            $this->value_type_name = $value_type_args;
        } else {
            $value_type_args = $this->parse_type_args($value_type_args);
            $this->value_type_name = $this->new_temp_type($value_type_args);
        }

    }

    public function parse_type_args($args)
    {
        if(is_string($args)) {
            $args = [$args];

        } elseif (! is_array($args)) {
            $args = $this->convert_to_string($args);
            throw new Exception("The enumeration args are invalid: $args");

        } elseif (! is_string($args[0])) {
            array_unshift($args, 'integer');
        }

        return $args;
    }

    public function validate_type_properties($value_type_args)
    {
        if ($this->type_exists($value_type_args)) {
            $this->value_type_name = $value_type_args;
        } else {
            $value_type_args = $this->parse_type_args($value_type_args);
            $this->value_type_name = $this->new_temp_type($value_type_args);
        }

        $key_types_args = func_get_args();
        array_shift($key_types_args);

        if (! $key_types_args) {
            $this->value_type_name[] = self::new_temp_type(['integer']);

        } else {
            foreach($key_types_args as $key_type_args) {
                if (!$this->type_exists($value_type_args)) {
                    $value_type_args = $this->parse_type_args($value_type_args);
                    $this->value_type_name = self::new_temp_type($value_type_args);
                }

                $this->value_type_name[] = self::new_temp_type($key_type_args);
            }
        }
    }
}
