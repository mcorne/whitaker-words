<?php
require_once 'common.php';

class inflection extends common
{
    /**
     * eg "ADJ 1 1 NOM S M POS 1 2 us X A"
     * @var array
     */
    public $adjective_attributes = [
        'which',
        'variant',
        'cases',
        'number',
        'gender',
        'comparison',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    /**
     * eg "ADV POS 1 0 X A"
     * @var array
     */
    public $adverb_attributes = [
        'comparison',
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

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

    public $comparison_type = [
        'X',     // all, none, or unknown
        'POS',   // POSitive
        'COMP',  // COMParative
        'SUPER', // SUPERlative
    ];

    /**
     * eg "CONJ 1 0 X A"
     * @var array
     */
    public $conjunction_attributes = [
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

    public $ending_size_type = [0, 1, 2, 3, 4, 5, 6, 7];

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

    public $gender_type = [
        'X', // all, none, or unknown
        'M', // Masculine
        'F', // Feminine
        'N', // Neuter
        'C', // Common (masculine and/or feminine)
    ];

    public $inflection_line_numbers;

    /**
     * eg "INTERJ 1 0 X A"
     * @var array
     */
    public $interjection_attributes = [
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

    public $mood_type = [
        'X',   // all, none, or unknown
        'IND', // INDicative
        'SUB', // SUBjunctive
        'IMP', // IMPerative
        'INF', // INFinative
        'PPL', // ParticiPLe
    ];

    /**
     * eg "N 1 1 NOM S C  1 1 a X A"
     * @var array
     */
    public $noun_attributes = [
        'which',
        'variant',
        'cases',
        'number',
        'gender',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $number_type = [
        'X', // all, none, or unknown
        'S', // Singular
        'P', // Plural
    ];

    /**
     * eg "NUM 1 1 NOM S M CARD 1 2 us X A"
     * @var array
     */
    public $numeral_attributes = [
        'which',
        'variant',
        'cases',
        'number',
        'gender',
        'numeral_sort',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $numeral_sort_type = [
        'X',      // all, none, or unknown
        'CARD',   // CARDinal
        'ORD',    // ORDinal
        'DIST',   // DISTributive
        'ADVERB', // numeral ADVERB
    ];

    /**
     * eg "VPAR 1 0 NOM S X PRES ACTIVE PPL 1 3 ans X A"
     * @var array
     */
    public $participle_attributes = [
        'which',
        'variant',
        'cases',
        'number',
        'gender',
        'tense',
        'voice',
        'mood',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $parts_of_speech = [
        'ADJ'    => 'adjective',
        'ADV'    => 'adverb',
        'CONJ'   => 'conjunction',
        'INTERJ' => 'interjection',
        'N'      => 'noun',
        'NUM'    => 'numeral',
        // 'PACK'   => 'propack',        // artificial for code
        // 'PREFIX' => 'prefix',         // artificial for code
        'PREP'   => 'preposition',
        'PRON'   => 'pronoun',
        // 'SUFFIX' => 'suffix',         // artificial for code
        'SUPINE' => 'supine',
        // 'TACKON' => 'tackon',         // artificial for code
        'V'      => 'verb',
        'VPAR'   => 'participle',
    ];

    public $person_type = [0, 1, 2, 3];

    /**
     * eg "PREP GEN 1 0 X A"
     * @var array
     */
    public $preposition_attributes = [
        'cases',
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

    /**
     * eg "PRON 1 0 GEN S X 2 3 jus X A"
     * @var array
     */
    public $pronoun_attributes = [
        'which',
        'variant',
        'cases',
        'number',
        'gender',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $stem_key_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * eg "SUPINE 0 0 ACC S N 4 2 um X A"
     * @var array
     */
    public $supine_attributes = [
        'which',
        'variant',
        'cases',
        'number',
        'gender',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $tense_type = [
        'X',    // all, none, or unknown
        'PRES', // PRESent
        'IMPF', // IMPerFect
        'FUT',  // FUTure
        'PERF', // PERFect
        'PLUP', // PLUPerfect
        'FUTP', // FUTure Perfect
    ];

    public $variant_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * eg "V 1 1 PRES ACTIVE IND  2 S  2 2 as X A"
     * @var array
     */
    public $verb_attributes = [
        'which',
        'variant',
        'tense',
        'voice',
        'mood',
        'person',
        'number',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $voice_type = [
        'X',       // all, none, or unknown
        'ACTIVE',  // ACTIVE
        'PASSIVE', // PASSIVE
    ];

    public $which_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    public function combine_inflection_attributes_and_values($attributes, $values, $property)
    {
        if (isset($attributes['ending'])) {
            if (! isset($attributes['ending_size'])) {
                throw new Exception("Ending size attribute missing in: $property.");
            }

            $ending_size_index = $attributes['ending_size'];

            if ($values[$ending_size_index] == 0) {
                // there is no ending value, removes the ending key
                unset($attributes['ending']);
            }
        }

        $inflection = $this->combine_attributes_and_values($attributes, $values);

        return $inflection;
    }

    public function create_inflection_table()
    {
        $sql = '
            DROP TABLE IF EXISTS inflection;

            VACUUM;

            CREATE TABLE inflection (
                id             INTEGER PRIMARY KEY AUTOINCREMENT,
                part_of_speech TEXT NOT NULL,
                which          INTEGER,
                variant        INTEGER,
                cases          TEXT,
                number         TEXT,
                gender         TEXT,
                comparison     TEXT,
                numeral_sort   TEXT,
                tense          TEXT,
                voice          TEXT,
                mood           TEXT,
                person         INTEGER,
                stem_key       INTEGER NOT NULL,
                ending_size    INTEGER NOT NULL,
                ending         TEXT,
                age            TEXT NOT NULL,
                frequency      TEXT NOT NULL,
                line_number    INTEGER NOT NULL
            );
        ';

        $this->pdo->exec($sql);
    }

    public function insert_inflection($inflection)
    {
        $colums = implode(',', array_keys($inflection));
        $values = array_map([$this->pdo, 'quote'], array_values($inflection));
        $values = implode(',', $values);
        $sql = "INSERT INTO inflection ($colums) VALUES ($values)";

        $this->pdo->exec($sql);
    }

    public function insert_inflections($inflections)
    {
        $this->pdo->exec('BEGIN TRANSACTION');
        array_map([$this, 'insert_inflection'], $inflections);
        $this->pdo->exec('COMMIT TRANSACTION');
    }

    public function load_inflections($lines = null)
    {
        if (! $lines) {
            $lines = $this->read_lines(__DIR__ . '/../data/INFLECTS.LAT');
        }

        $inflections = $this->parse_inflections($lines);
        $this->create_inflection_table();
        $this->insert_inflections($inflections);

        return $inflections;
    }

    public function parse_inflection($line)
    {
        list($values, $part_of_speech) = $this->split_inflection($line);

        $property = $this->parts_of_speech[$part_of_speech] . '_attributes';
        $attributes = $this->$property;

        $inflection = $this->combine_inflection_attributes_and_values($attributes, $values, $property);

        foreach ($inflection as $attribute => $value) {
            if ($attribute != 'ending') {
                $this->validate_inflection_value($attribute, $value);
            }
        }

        if (isset($inflection['ending'])) {
            $this->validate_ending_size($inflection);
        }

        $parsed['part_of_speech'] = $part_of_speech;
        $parsed += $inflection;
        $parsed['line_number'] = $this->line_number;

        return $parsed;
    }

    public function parse_inflections($lines)
    {
        $inflections = [];

        foreach ($lines as $index => $line) {
            list($line) = explode('--', $line);

            if (! $line = trim($line)) {
                continue;
            }

            $this->line_number = $index + 1;

            $inflections[] = $this->parse_inflection($line);
        }

        return $inflections;
    }

    public function split_inflection($line)
    {
        $values = preg_split('~ +~', $line, null, PREG_SPLIT_NO_EMPTY);
        $hash = implode('|', $values);

        if (isset($this->inflection_line_numbers[$hash])) {
            $message = $this->set_error_message('Duplicate inflection, same as line: %d', $this->inflection_line_numbers[$hash]);
            throw new Exception($message);
        }

        $this->inflection_line_numbers[$hash] = $this->line_number;

        $part_of_speech = array_shift($values);

        if (! isset($this->parts_of_speech[$part_of_speech])) {
            $message = $this->set_error_message('Invalid part of speech: %s.', $part_of_speech);
            throw new Exception($message);
        }

        return [$values, $part_of_speech];
    }

    public function validate_ending_size($inflection)
    {
        $ending_size = strlen($inflection['ending']);

        if ($inflection['ending_size'] != $ending_size) {
            $message = $this->set_error_message('Ending and size do not match: %d != %d.', $ending_size, $inflection['ending_size']);
            throw new Exception($message);
        }
    }

    public function validate_inflection_value($attribute, $value)
    {
        $property = $attribute . '_type';

        if (! isset($this->$property)) {
            throw new Exception("Invalid property: $property.");
        }

        $attribute_values = $this->$property;

        if (! isset($attribute_values[$value])) {
            $message = $this->set_error_message('Invalid inflection value: %s => %s.', $attribute, $value);
            throw new Exception($message);
        }
    }
}
