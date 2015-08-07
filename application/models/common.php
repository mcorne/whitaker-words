<?php
class common
{
    public $age_type = [
        'X', //             --  In use throughout the ages/unknown -- the default
        'A', // archaic     --  Very early forms, obsolete by classical times
        'B', // early       --  Early Latin, pre-classical, used for effect/poetry
        'C', // classical   --  Limited to classical (~150 BC - 200 AD)
        'D', // late        --  Late, post-classical (3rd-5th centuries)
        'E', // later       --  Latin not in use in Classical times (6-10), Christian
        'F', // medieval    --  Medieval (11th-15th centuries)
        'G', // scholar     --  Latin post 15th - Scholarly/Scientific   (16-18)
        'H', // modern      --  Coined recently, words for new things (19-20)
    ];

    public $comparison_type = [
        'X',     // all, none, or unknown
        'POS',   // POSitive
        'COMP',  // COMParative
        'SUPER', // SUPERlative
    ];

    public $entry_line_numbers;

    public $frequency_type = [
        'X', //             --  Unknown or unspecified
        'A', // very freq   --  Very frequent, in all Elementry Latin books
        'B', // frequent    --  Frequent, in top 10 percent
        'C', // common      --  For Dictionary, in top 10,000 words
        'D', // lesser      --  For Dictionary, in top 20,000 words
        'E', // uncommon    --  2 or 3 citations
        'F', // very rare   --  Having only single citation in OLD or L+S
        'I', // inscription --  Only citation is inscription
        'M', // graffiti    --  Presently not much used
        'N', // Pliny       --  Things that appear (almost) only in Pliny Natural History
    ];

    public $line_number;

    public $parts_of_speech;

    public $pdo;

    public $variant_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    public $which_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    public function __construct()
    {
        $this->flip_properties();

        $this->connect_to_database();
    }

    public function combine_attributes_and_values($attributes, $values)
    {
        $attributes_count = count($attributes);
        $values_count     = count($values);

        if ($attributes_count != $values_count) {
            $message = $this->set_error_message('Attributes and values do not match: %d != %d.', $attributes_count, $values_count);
            throw new Exception($message);
        }

        $attributes = array_keys($attributes);
        $combined = array_combine($attributes, $values);

        return $combined;
    }

    public function connect_to_database()
    {
        $dsn = sprintf('sqlite:%s/../data/whitaker.sqlite', __DIR__);
        $this->pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function flip_properties()
    {
        $properties = get_object_vars($this);

        foreach ($properties as $property => $values) {
            if (preg_match('~_(attributes|type)$~', $property)) {
                $this->$property = array_flip($values);
            }
        }
    }

    public function parse_entries($lines)
    {
        $entries = [];

        foreach ($lines as $index => $line) {
            list($line) = explode('--', $line);

            if (! $line = trim($line)) {
                continue;
            }

            $this->line_number = $index + 1;

            $entries[] = $this->parse_entry($line);
        }

        return $entries;
    }

    public function parse_entry($line)
    {
        throw new Exception(__FUNCTION__ . '() not implemented' );
    }

    public function read_lines($filename)
    {
        if (! $lines = @file($filename)) {
            throw new Exception("Cannot read: $filename");
        }

        return $lines;
    }

    /**
     *
     * @param string $format
     * @param string $arg1
     * @param string $arg2 etc.
     * @return string
     */
    public function set_error_message()
    {
        $args = func_get_args();
        $format = "Error line #%d: " . array_shift($args);
        array_unshift($args, $this->line_number);
        $message = vprintf($format, $args);

        return $message;
    }

    public function validate_entry_value($attribute, $value)
    {
        $property = $attribute . '_type';

        if (! isset($this->$property)) {
            throw new Exception("Invalid property: $property.");
        }

        $attribute_values = $this->$property;

        if (! isset($attribute_values[$value])) {
            $message = $this->set_error_message('Invalid entry value: %s => %s.', $attribute, $value);
            throw new Exception($message);
        }
    }

    public function validate_part_of_speech($part_of_speech)
    {
        if (! isset($this->parts_of_speech[$part_of_speech])) {
            $message = $this->set_error_message('Invalid part of speech: %s.', $part_of_speech);
            throw new Exception($message);
        }
    }

    public function validate_unique_entry($values)
    {
        $hash = implode('|', $values);

        if (isset($this->entry_line_numbers[$hash])) {
            $message = $this->set_error_message('Duplicate entry, same as line: %d', $this->entry_line_numbers[$hash]);
            throw new Exception($message);
        }

        $this->entry_line_numbers[$hash] = $this->line_number;
    }
}