<?php
require_once 'common.php';

/**
 * Parsing of the inflection file and loading in the database
 */
class inflection extends common
{
    /**
     * The adjective attributes
     *
     * eg "ADJ 1 1 NOM S M POS 1 2 us X A"
     * @var array
     * @source source/inflections_package.ads ADJECTIVE_RECORD
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
     * The adverb attributes
     *
     * eg "ADV POS 1 0 X A"
     * @var array
     * @source source/inflections_package.ads ADVERB_RECORD
     */
    public $adverb_attributes = [
        'comparison',
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

    /**
     * The conjunction attributes
     *
     * eg "CONJ 1 0 X A"
     * @var array
     * @source source/inflections_package.ads CONJUNCTION_RECORD
     */
    public $conjunction_attributes = [
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

    /**
     * List of ending sizes
     *
     * @var array
     * @source source/inflections_package.ads ENDING_SIZE_TYPE
     */
    public $ending_size_type = [0, 1, 2, 3, 4, 5, 6, 7];

    /**
     * The interjection attributes
     *
     * eg "INTERJ 1 0 X A"
     * @var array
     * @source source/inflections_package.ads INTERJECTION_RECORD
     */
    public $interjection_attributes = [
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

    /**
     * List of verb moods
     *
     * @var array
     * @source source/inflections_package.ads MOOD_TYPE
     */
    public $mood_type = [
        'X',   // all, none, or unknown
        'IND', // INDicative
        'SUB', // SUBjunctive
        'IMP', // IMPerative
        'INF', // INFinative
        'PPL', // ParticiPLe
    ];

    /**
     * The noun attributes
     *
     * eg "N 1 1 NOM S C  1 1 a X A"
     * @var array
     * @source source/inflections_package.ads NOUN_RECORD
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

    /**
     * List of number types
     *
     * @var array
     * @source source/inflections_package.ads NUMBER_TYPE
     */
    public $number_type = [
        'X', // all, none, or unknown
        'S', // Singular
        'P', // Plural
    ];

    /**
     * The numeral attributes
     *
     * eg "NUM 1 1 NOM S M CARD 1 2 us X A"
     * @var array
     * @source source/inflections_package.ads NUMERAL_RECORD
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

    /**
     * The verb participle attributes
     *
     * eg "VPAR 1 0 NOM S X PRES ACTIVE PPL 1 3 ans X A"
     * @var array
     * @source source/inflections_package.ads VPAR_RECORD
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

    /**
     * List of parts of speech
     *
     * @var array
     * @source source/inflections_package.ads PART_OF_SPEECH_TYPE
     */
    public $parts_of_speech = [
        'ADJ'    => 'adjective',
        'ADV'    => 'adverb',
        'CONJ'   => 'conjunction',
        'INTERJ' => 'interjection',
        'N'      => 'noun',
        'NUM'    => 'numeral',
        'PREP'   => 'preposition',
        'PRON'   => 'pronoun',
        'SUPINE' => 'supine',
        'V'      => 'verb',
        'VPAR'   => 'participle',
    ];

    /**
     * List of verb persons
     *
     * @var array
     * @source source/inflections_package.ads PERSON_TYPE
     */
    public $person_type = [0, 1, 2, 3];

    /**
     * The preposition attributes
     *
     * eg "PREP GEN 1 0 X A"
     * @var array
     * @source source/inflections_package.ads PREPOSITION_RECORD
     */
    public $preposition_attributes = [
        'cases',
        'stem_key',
        'ending_size',
        'age',
        'frequency',
    ];

    /**
     * The pronoun attributes
     *
     * eg "PRON 1 0 GEN S X 2 3 jus X A"
     * @var array
     * @source source/inflections_package.ads PRONOUN_RECORD
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

    /**
     * The inflection table definition
     *
     * @var string
     */
    public $sql_table = '
        DROP TABLE IF EXISTS inflection;
        VACUUM;

        CREATE TABLE inflection (
        id             INTEGER PRIMARY KEY,
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
        line_number    INTEGER NOT NULL);
    ';

    /**
     * The inflection views and indexes definition
     *
     * @var string
     * @see word::$sql_selects that leverages indexes
     */
    public $sql_views_and_indexes = '
        DROP INDEX IF EXISTS inflection_adjective;
        CREATE INDEX inflection_adjective ON inflection (part_of_speech, which, variant, comparison);

        DROP INDEX IF EXISTS inflection_noun;
        CREATE INDEX inflection_noun ON inflection (part_of_speech, which, variant, gender);

        DROP VIEW IF EXISTS inflections_by_part_of_speech;
        CREATE VIEW inflections_by_part_of_speech AS
        SELECT
            part_of_speech,
            count(part_of_speech) AS count
        FROM inflection group by part_of_speech
        UNION
        SELECT
            "-- Total --" AS part_of_speech,
            count(*) AS count
        FROM inflection;
    ';

    /**
     * List of verb tenses
     *
     * @var array
     * @source source/inflections_package.ads TENSE_TYPE
     */
    public $stem_key_type = [1, 2, 3, 4];

    /**
     * The supine attributes
     *
     * eg "SUPINE 0 0 ACC S N 4 2 um X A"
     * @var array
     * @source source/inflections_package.ads SUPINE_RECORD
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

    /**
     * List of verb tenses
     *
     * @var array
     * @source source/inflections_package.ads TENSE_TYPE
     */
    public $tense_type = [
        'X',    // all, none, or unknown
        'PRES', // PRESent
        'IMPF', // IMPerFect
        'FUT',  // FUTure
        'PERF', // PERFect
        'PLUP', // PLUPerfect
        'FUTP', // FUTure Perfect
    ];

    /**
     * Inflection parsing basic tests
     *
     * @var array
     */
    public $test_lines = [
        'ADJ    1 1 NOM S M POS             1 2 us  X A',
        'ADV        POS                     1 0     X A',
        'CONJ                               1 0     X A',
        'INTERJ                             1 0     X A',
        'N      1 1 NOM S C                 1 1 a   X A',
        'NUM    1 1 NOM S M CARD            1 2 us  X A',
        'VPAR   1 0 NOM S X PRES ACTIVE PPL 1 3 ans X A',
        'PREP       GEN                     1 0     X A',
        'PRON   1 0 GEN S X                 2 3 jus X A',
        'SUPINE 0 0 ACC S N                 4 2 um  X A',
        'V      1 1 PRES ACTIVE IND 2 S     2 2 as  X A',
    ];

    /**
     * The verb attributes
     *
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

    /**
     * List of verb voices
     *
     * @var array
     * @source source/inflections_package.ads VOICE_TYPE
     */
    public $voice_type = [
        'X',       // all, none, or unknown
        'ACTIVE',  // ACTIVE
        'PASSIVE', // PASSIVE
    ];

    public function __construct()
    {
        parent::__construct();

        $this->flip_properties();
    }

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

    public function load_inflections()
    {
        $lines = $this->read_lines(__DIR__ . '/../data/INFLECTS.LAT');

        $inflections = $this->parse_entries($lines);
        $count = $this->load_table('inflection', $inflections);

        return $count;
    }

    public function parse_entry($line, $inflection_id)
    {
        list($values, $part_of_speech) = $this->split_inflection($line);

        $property = $this->parts_of_speech[$part_of_speech] . '_attributes';
        $attributes = $this->$property;

        $inflection = $this->combine_inflection_attributes_and_values($attributes, $values, $property);

        foreach ($inflection as $attribute => $value) {
            if ($attribute != 'ending') {
                $this->validate_entry_value($attribute, $value);
            }
        }

        if (isset($inflection['ending'])) {
            $this->validate_ending_size($inflection);
        }

        $inflection['part_of_speech'] = $part_of_speech;
        $inflection['line_number'] = $this->line_number;
        $inflection['id'] = $inflection_id;

        return $inflection;
    }

    public function split_inflection($line)
    {
        $values = preg_split('~ +~', $line, null, PREG_SPLIT_NO_EMPTY);
        $this->validate_unique_entry($values);
        $part_of_speech = array_shift($values);
        $this->validate_part_of_speech($part_of_speech);

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
}
