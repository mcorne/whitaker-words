<?php
/**
 * Basic support to handle the database and the text files.
 */
class common
{
    /**
     * The "attribute" and "type" related properties have a reserved "_attribute" or "_type" suffix.
     * These properties are usually flipped in child classes.
     *
     * @see self::flip_properties()
     */

    /**
     * List of ages used in inflections and dictionary entries
     *
     * @var array
     * @source source/inflections_package.ads AGE_TYPE
     */
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

    /**
     * List of Latin cases used in inflections and dictionary entries
     *
     * @var array
     * @source source/inflections_package.ads CASE_TYPE
     */
    public $cases_type = [
        'X',   // all, none, or unknown
        'NOM', // NOMinative
        'VOC', // VOCative
        'GEN', // GENitive
        'LOC', // LOCative
        'DAT', // DATive
        'ABL', // ABLative
        'ACC', // ACCusitive
    ];

    /**
     * List of comparison types used for adverbs and adjectives in inflections and dictionary entries
     *
     * @var array
     * @source source/inflections_package.ads COMPARISON_TYPE
     */
    public $comparison_type = [
        'X',     // all, none, or unknown
        'POS',   // POSitive
        'COMP',  // COMParative
        'SUPER', // SUPERlative
    ];

    /**
     * Entry hashes used to spot possible entry duplicates
     *
     * @var array
     */
    public $entry_hashes;

    /**
     * List of usage frequencies used in inflections and dictionary entries
     *
     * @var array
     * @source source/inflections_package.ads FREQUENCY_TYPE
     */
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

    /**
     * List of Latin genders used in inflections and dictionary entries
     *
     * @var array
     * @source source/inflections_package.ads GENDER_TYPE
     */
    public $gender_type = [
        'X', // all, none, or unknown
        'M', // Masculine
        'F', // Feminine
        'N', // Neuter
        'C', // Common (masculine and/or feminine)
    ];

    /**
     * Current entry line being read
     *
     * @var int
     */
    public $line_number;

    /**
     * List of numeral types used in inflections and dictionary entries
     *
     * @var array
     * @source source/inflections_package.ads NUMERAL_SORT_TYPE
     */
    public $numeral_sort_type = [
        'X',      // all, none, or unknown
        'CARD',   // CARDinal
        'ORD',    // ORDinal
        'DIST',   // DISTributive
        'ADVERB', // numeral ADVERB
    ];

    /**
     * List of parts of speech
     *
     * Must be defined in the child class.
     *
     * @var array
     */
    public $parts_of_speech;

    /**
     * Database PDO instance
     *
     * @var PDO
     * @see self::__constructor()
     */
    public $pdo;

    /**
     * SQL statement to create the main table
     *
     * Must be defined in the child class.
     *
     * @var string
     */
    public $sql_table;

    /**
     * SQL statements to create views and indexes
     *
     * To be defined in the child class if needed.
     *
     * @var sting
     */
    public $sql_views_and_indexes;

    /**
     * Basic tests
     *
     * To be defined in the child class if needed.
     *
     * @var array
     */
    public $test_lines;

    /**
     * List of inflection variants
     *
     * @var array
     * @source source/inflections_package.ads VARIANT_TYPE
     */
    public $variant_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * List of inflection ID numbers
     *
     * @var array
     * @source source/inflections_package.ads WHICH_TYPE
     */

    public $which_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * Connection to the database
     */
    public function __construct()
    {
        $this->connect_to_database();
    }

    /**
     * Combines the infection attributes and values
     *
     * @param array $attributes
     * @param array $values
     * @return array the entry
     * @throws Exception
     */
    public function combine_attributes_and_values($attributes, $values)
    {
        $attributes_count = count($attributes);
        $values_count     = count($values);

        if ($attributes_count != $values_count) {
            $message = $this->set_error_message('Attributes and values do not match: %d != %d.', $attributes_count, $values_count);
            throw new Exception($message);
        }

        $attributes = array_keys($attributes);
        $inflection = array_combine($attributes, $values);

        return $inflection;
    }

    /**
     * Connects to the database
     */
    public function connect_to_database()
    {
        $dsn = sprintf('sqlite:%s/../data/whitaker.sqlite', __DIR__);
        $this->pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    /**
     * Flips the values of attribute and type properties
     *
     * Flipped properties allow to test a value with isset(), eg isset($this->cases_type['NOM']).
     * The attribute and type properties have the "_attribute" or "_type" suffix.
     */
    public function flip_properties()
    {
        $properties = get_object_vars($this);

        foreach ($properties as $property => $values) {
            if (preg_match('~_(attributes|type)$~', $property)) {
                // this is an attribute or type property, flips the property
                $this->$property = array_flip($values);
            }
        }
    }

    /**
     * Returns the entry stem corresponding to an inflection stem key
     *
     * @param int $stem_key
     * @param array $entry
     * @param int $inflection_id
     * @return string the stem
     * @throws Exception
     */
    public function get_stem($stem_key, $entry, $inflection_id)
    {
        switch ($stem_key) {
            case 1:
                $stem = $entry['stem1'];
                break;
            case 2:
                $stem = $entry['stem2'];
                break;
            case 3:
                $stem = $entry['stem3'];
                break;
            case 4:
                $stem = $entry['stem4'];
                break;
            default:
                throw new Exception("Invalid stem key: $stem_key in inflection id: $inflection_id");
        }

        return $stem;
    }

    /**
     * Inserts inflection or dictionary entries in the table
     *
     * This method can be overloaded to handle entries that need custom processing, eg word entries
     *
     * @param string $table_name
     * @param array $entries
     * @return int the number of entries
     * @see word::insert_entries()
     */
    public function insert_entries($table_name = null, $entries = null)
    {
        foreach ($entries as $entry) {
            $this->insert_entry($table_name, $entry);
        }

        return count($entries);
    }

    /**
     * Inserts the entry inflected words in the table
     *
     * @param string $table_name
     * @param array $entry
     */
    public function insert_entry($table_name, $entry)
    {
        $colums = implode(',', array_keys($entry));
        $values = array_map([$this->pdo, 'quote'], array_values($entry));
        $values = implode(',', $values);
        $sql = "INSERT INTO $table_name ($colums) VALUES ($values)";

        $this->pdo->exec($sql);
    }

    /**
     * Loads entries into a table
     *
     * @param string $table_name
     * @param array $entries
     * @return int the number of entries
     */
    public function load_table($table_name, $entries = null)
    {
        $this->pdo->exec($this->sql_table);

        $this->pdo->exec('BEGIN TRANSACTION');
        $count = $this->insert_entries($table_name, $entries);
        $this->pdo->exec('COMMIT TRANSACTION');

        $this->pdo->exec($this->sql_views_and_indexes);

        return $count;
    }

    /**
     * Returns the lines of a file parsed into arrays
     *
     * @param array $lines
     * @return array the entries
     */
    public function parse_entries($lines)
    {
        $entries = [];
        $entry_id = 0;

        foreach ($lines as $index => $line) {
            // removes ADA-like comments
            list($line) = explode('--', $line);

            if (! $line = trim($line)) {
                // ignores empty lines
                continue;
            }

            $this->line_number = $index + 1;
            $entry_id++;

            $entries[] = $this->parse_entry($line, $entry_id);
        }

        return $entries;
    }

    /**
     * Parses an entry
     *
     * Must be implemented in the child class.
     *
     * @param string $line
     * @param int $entry_id
     * @throws Exception
     */
    public function parse_entry($line, $entry_id)
    {
        throw new Exception(__FUNCTION__ . '() not implemented' );
    }

    /**
     * Reads the lines of a file
     *
     * @param string $filename
     * @return array the lines
     * @throws Exception
     */
    public function read_lines($filename)
    {
        if (! $lines = @file($filename)) {
            throw new Exception("Cannot read: $filename");
        }

        return $lines;
    }

    /**
     * Prefixes the error message with the line number
     *
     * @param string $format the printf() like format
     * @param string $arg1
     * @param string $arg2 etc.
     * @return string the error message
     */
    public function set_error_message()
    {
        $args = func_get_args();
        $format = "Error line #%d: " . array_shift($args);
        array_unshift($args, $this->line_number);
        $message = vprintf($format, $args);

        return $message;
    }

    /**
     * Returns parsed entries for testing purposes
     *
     * @return array the entries
     */
    public function test_parsing()
    {
        $entries = [];

        foreach ($this->test_lines as $index => $line) {
            $entries[] = [
                'line'  => $line,
                'entry' => $this->parse_entry($line, $index + 1),
            ];
        }

        return $entries;
    }

    /**
     * Verifies an attribute value is in the list of the attribute values
     *
     * @param string $attribute
     * @param string|int $value
     * @throws Exception
     */
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

    /**
     * Verifies the part of speech is valid
     *
     * @param string $part_of_speech
     * @throws Exception
     */
    public function validate_part_of_speech($part_of_speech)
    {
        if (! isset($this->parts_of_speech[$part_of_speech])) {
            $message = $this->set_error_message('Invalid part of speech: %s.', $part_of_speech);
            throw new Exception($message);
        }
    }

    /**
     * Verifies an entry is unique
     *
     * @param array $values
     * @throws Exception
     */
    public function validate_unique_entry($values)
    {
        $hash = implode('|', $values);

        if (isset($this->entry_hashes[$hash])) {
            $message = $this->set_error_message('Duplicate entry, same as line: %d', $this->entry_hashes[$hash]);
            throw new Exception($message);
        }

        $this->entry_hashes[$hash] = $this->line_number;
    }
}
