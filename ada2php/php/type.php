<?php
abstract class type
{
    protected $data;

    // the property $value MUST NOT be added in children

    /**
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        if (is_null($value)) {
            return;
        }

        $this->__set('value', $value);
    }

    /**
     *
     * @param string $name
     */
    public function __get($name)
    {
        if ($name != 'value') {
            throw new Exception("Undefined property: $name");
        }

        return $this->data;
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if ($name != 'value') {
            throw new Exception("Invalid property: $name");
        }

        $this->validate($value);
        $this->set_value($value);
    }

    public function __toString()
    {
        if (! is_scalar($this->data)) {
            throw new Exception("Cannot convert non scalar to string");
        }

        return (string) $this->data;
    }

    public static function add($type_name, $arg1 = null, $arg2 = null)
    {
        if (class_exists($type_name, false)) {
            throw new Exception("Type already exists: $type_name");
        }

        static::validate_type($arg1, $arg2);
        static::create_type($type_name, $arg1, $arg2);
    }

    /**
     *
     * @param mixed $value
     * @return object
     */
    public static function create($value = null)
    {
        return new static($value);
    }

    /**
     *
     * @param string $type_name
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public static function create_type($type_name, $arg1 = null, $arg2 = null)
    {
        throw new Exception("Type creation method unavailable for $type_name");
    }

    /**
     *
     * @param string $type_name
     * @param mixed $value
     * @return object
     * @throws Exception
     */
    public static function factory($type_name, $value)
    {
        if (! class_exists($type_name, false)) {
            throw new Exception("Unavailable class: $type_name");
        }

        return new $type_name($value);
    }

    abstract public function set_value($value);

    abstract public function validate($value);

    /**
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @throws Exception
     */
    public static function validate_type($arg1 = null, $arg2 = null)
    {
        throw new Exception('Type validation method unavailable');
    }
}
