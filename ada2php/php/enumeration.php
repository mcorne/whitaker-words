<?php
require_once 'type.php';

class enumeration extends type
{
    protected $indexes;
    protected $values;

    /**
     * Creates the enumeration sub type class
     *
     * @param string $parent_type_name
     * @param string $type_name
     * @param string $first
     * @param string $last
     * @param array $values
     * @param array $indexes
     * @return string
     */
    public function create_type_class($parent_type_name, $type_name, $first = null, $last = null, $values = null, $indexes = null)
    {
        $exported_values  = var_export($values, true);
        $exported_indexes = var_export($indexes, true);
        $first = addslashes($first);
        $last  = addslashes($last);

        $class = "
            class $type_name extends $parent_type_name
            {
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
     * Creates the enumeration sub type class
     *
     * @param string $parent_type_name
     * @param string $type_name
     * @param int $first
     * @param int $last
     * @return string
     */
    public function filter_and_validate_enumeration($values = null)
    {
        if (! is_array($values)) {
            $values = $this->convert_to_string($values);
            throw new Exception("The enumeration is not an array: $values.");
        }

        $filtered_values = [];
        $indexes = [];

        foreach (array_values($values) as $index => $value) {
            $value = $this->filter_and_validate($value);

            if (isset($indexes[$value])) {
                throw new Exception("The enumeration value already exists: $value.");
            }

            $filtered_values[$index] = $value;
            $indexes[$value] = $index;
        }

        return [$filtered_values, $indexes];
    }

    public function get_range()
    {
        return $this->values;
    }

    /**
     *
     * @param string $first
     * @param string $last
     */
    public function is_valid_range($first, $last)
    {
        $first = $this->filter_and_validate($first);
        $last  = $this->filter_and_validate($last);

        if ($this->indexes[$first] > $this->indexes[$last]) {
            throw new Exception("The first enumeration value is greater than the second one: $first > $last.");
        }
    }

    /**
     *
     * @param string $value
     * @return int
     */
    public function pos_dynamic($value)
    {
        $value = $this->filter_and_validate($value);

        return $this->indexes[$value];
    }

    /**
     *
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function pred_dynamic($value)
    {
        $value = $this->filter_and_validate($value);

        if ($value == $this->first) {
            throw new Exception('The first enumeration value has no predecessor.');
        }

        $pred_index = $this->indexes[$value] - 1;

        return $this->values[$pred_index];
    }

    /**
     *
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function succ_dynamic($value)
    {
        $value = $this->filter_and_validate($value);

        if ($value == $this->last) {
            throw new Exception('The last enumeration value has no successor.');
        }

        $pred_index = $this->indexes[$value] + 1;

        return $this->values[$pred_index];
    }

    /**
     *
     * @param int $index
     * @return string
     */
    public function val_dynamic($index)
    {
        if (! is_int($index) or ! isset($this->values[$index])) {
            $index = $this->convert_to_string($index);
            throw new Exception("The index is invalid: $index");
        }

        return $this->values[(int) $index];
    }

    /**
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @return array
     */
    public function validate_type_properties($arg1 = null, $arg2 = null)
    {
        if (get_class($this) == __CLASS__) {
            // this is a new enumeration
            $values = $arg1;
            list($values, $indexes) = $this->filter_and_validate_enumeration($values);
            $first = current($values);
            $last  = end($values);

        } else {
            // this is a subset of an existing enumeration
            $first = $arg1;
            $last  = $arg2;
            $this->is_valid_range($first, $last);
            list($values, $indexes) = $this->extract_sub_range_values($first, $last);
        }

        return [$first, $last, $values, $indexes];
    }

    /**
     *
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function validate_value($value)
    {
        if (! is_string($value) or ! is_null($this->indexes) and ! isset($this->indexes[$value])) {
            $value = $this->convert_to_string($value);
            throw new Exception("The enumeration value is invalid: $value.");
        }

        return $value;
    }
}
