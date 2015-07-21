<?php
require_once 'type.php';

class enumeration extends type
{
    public $indexes;
    public $values;

    /**
     *
     * @param string $type_name
     * @param mixed $values
     * @param null $unused
     * @return string
     */
    public function create_type_class($type_name, $values = null, $unused = null)
    {
        $exported_value   = var_export($values, true);
        $exported_indexes = var_export($this->indexes, true);

        $class = "
            class $type_name extends enumeration{
                public \$indexes = $exported_indexes;
                public \$values  = $exported_value;
            }
            ";

        return $class;
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
     * @throws Exception
     */
    public function validate($value)
    {
        if (! is_null($value) and (! is_string($value) or ! isset($this->indexes[$value]))) {
            throw new Exception('Invalid enumeration value');
        }
    }

    /**
     *
     * @param array $values
     * @throws Exception
     */
    public function validate_type_properties($values = null, $unused = null)
    {
        if (!  is_array($values)) {
            throw new Exception('Enumeration not an array');
        }

        foreach($values as $index => $value) {
            if (! is_string($value)) {
                throw new Exception('Non string enumeration value');
            }

            if (isset($this->indexes[$value])) {
                throw new Exception("Enumeration value already exists: $value");
            }

            $this->indexes[$value] = $index;
        }
    }
}
