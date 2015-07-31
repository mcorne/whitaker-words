<?php
require_once 'enumeration.php';

class boolean extends enumeration
{
    protected $first   = false;
    protected $indexes = [false => 0, true => 1];
    protected $last    = true;
    protected $values  = [false, true];

    /**
     *
     * @param bool $value
     * @return bool
     * @throws Exception
     */
    public function validate_value($value)
    {
        if (! is_bool($value)) {
            $value = $this->convert_to_string($value);
            throw new Exception("The value is not boolean: $value.");
        }

        return $value;
    }

}