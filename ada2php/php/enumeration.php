<?php
require_once 'type.php';

class enumeration extends type
{
    protected $indexes;
    protected $values;

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @return string
     */
    public function create_type_class($type_name, $arg1 = null, $arg2 = null)
    {
        if (get_class($this) == __CLASS__) {
            $values  = $arg1;
            $indexes = $this->indexes;
            $first   = current($values);
            $last    = end($values);

        } else {
            $first = $arg1;
            $last  = $arg2;
            list($values, $indexes) = $this->extract_sub_range_values($arg1, $arg2);
        }

        $exported_values  = var_export($values, true);
        $exported_indexes = var_export($indexes, true);
        $first = addslashes($first);
        $last  = addslashes($last);

        $class = "
            class $type_name extends enumeration{
                protected \$first   = '$first';
                protected \$indexes = $exported_indexes;
                protected \$last    = '$last';
                protected \$values  = $exported_values;
            }
            ";

        return $class;
    }

    /**
     *
     * @param string $first
     * @param string $last
     * @return array
     */
    public function extract_sub_range_values($first, $last)
    {
        $first_index = $this->indexes[$first];
        $last_index  = $this->indexes[$last];
        $length = $last_index - $first_index + 1;
        $values  = array_slice($this->values , $first_index, $length, true);
        $indexes = array_slice($this->indexes, $first_index, $length, true);

        return [$values, $indexes];
    }

    /**
     *
     * @param array $values
     * @throws Exception
     */
    public function is_valid_enumeration($values = null, $unused = null)
    {
        if (! is_array($values)) {
            throw new Exception('The enumeration is not an array.');
        }

        foreach($values as $index => $value) {
            if (! is_string($value)) {
                throw new Exception('The enumeration value is not a string.');
            }

            if (isset($this->indexes[$value])) {
                throw new Exception("The enumeration value already exists: $value.");
            }

            $this->indexes[$value] = $index;
        }
    }

    /**
     *
     * @param int $index
     * @throws Exception
     */
    public function is_valid_index($index)
    {
        $this->is_integer_value($index);

        if (! isset($this->values[$index])) {
            throw new Exception("Invalid index: $index");
        }
    }

    /**
     *
     * @param string $first
     * @param string $last
     */
    public function is_valid_sub_range($first = null, $last = null)
    {
        if (! isset($this->indexes[$first])) {
            throw new Exception("Invalid enumeration value: $first");
        }

        if (! isset($this->indexes[$last])) {
            throw new Exception("Invalid enumeration value: $last");
        }

        if ($this->indexes[$first] > $this->indexes[$last]) {
            throw new Exception("The first value is greater than the second value: $first > $last.");
        }
    }

    /**
     *
     * @param mixed $value
     * @return string
     * @throws Exception
     */
    public function pred($value)
    {
        $value = $this->get_value($value);

        if ($value == $this->first) {
            throw new Exception('The first value has no predecessor.');
        }

        $pred_index = $this->indexes[$value] - 1;

        return $this->values[$pred_index];
    }

    /**
     *
     * @param mixed $value
     * @return type
     */
    public function pos($value)
    {
        $value = $this->get_value($value);

        return $this->indexes[$value];
    }

    /**
     *
     * @param mixed $value
     */
    public function set_value($value)
    {
        $this->data = $value;
    }

    /**
     *
     * @param mixed $value
     * @return mixed
     * @throws Exception
     */
    public function succ($value)
    {
        $value = $this->get_value($value);

        if ($value == $this->last) {
            throw new Exception('The last value has no successor.');
        }

        $pred_index = $this->indexes[$value] + 1;

        return $this->values[$pred_index];
    }

    /**
     *
     * @param int $index
     * @return string
     */
    public function val($index)
    {
        $this->is_valid_index($index);

        return $this->values[(int) $index];
    }

    /**
     *
     * @param mixed $value
     * @throws Exception
     */
    public function validate($value)
    {
        if (! is_null($value) and (! is_string($value) or ! isset($this->indexes[$value]))) {
            throw new Exception('Invalid enumeration value.');
        }
    }

    /**
     *
     * @param array $values
     * @throws Exception
     */
    public function validate_type_properties($arg1 = null, $arg2 = null)
    {
        if (get_class($this) == __CLASS__) {
            $this->is_valid_enumeration($arg1, $arg2);
        } else {
            $this->is_valid_sub_range($arg1, $arg2);
        }
    }
}
