<?php
require_once 'type.php';

class enumeration extends type
{
    public $values;
    public $indexes;

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
