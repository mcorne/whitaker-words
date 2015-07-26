<?php
require_once 'type.php';

class arrays extends type implements ArrayAccess
{
    protected $add_keys;
    protected $dimension;
    protected $keys;
    protected $type;

    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    public function offsetGet($key) {
        return $this->data[$key];
    }

    public function offsetSet($key, $value)
    {
        if (is_array($value)) {
            $value = new self($value, $this->add_keys, $this->level + 1);
        }

        if (is_null($key)) {
            $this->data[] = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    public function set_value($array, $add_keys = false, $dimension = 0)
    {
        $this->add_keys  = $add_keys;
        $this->dimension = $dimension;

        foreach ($array as $key => $value) {
            if ($add_keys) {
                $key = $this->keys[$dimension][$key];
            }

            $this->offsetSet($key, $value);
        }
    }
}
