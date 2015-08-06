<?php
require_once 'file.php';

class inflection
{
    /**
     * eg "ADJ 1 1 NOM S M POS 1 2 us X A"
     * @var array
     */
    public $adjective_attributes = [
        'which',
        'variant',
        'case',
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

    public $case_type = [
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

    public $line_number;

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
        'case',
        'number',
        'gender',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $noun_type = [
        'X', // unknown, nondescript
        'S', // Singular "only"           --  not really used
        'M', // plural or Multiple "only" --  not really used
        'A', // Abstract idea
        'G', // Group/collective Name -- Roman(s)
        'N', // proper Name
        'P', // a Person
        'T', // a Thing
        'L', // Locale, name of country/city
        'W', // a place Where
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
        'case',
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

    public $numeral_value_type;

    /**
     * eg "VPAR 1 0 NOM S X PRES ACTIVE PPL 1 3 ans X A"
     * @var array
     */
    public $participle_attributes = [
        'which',
        'variant',
        'case',
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
        'case',
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
        'case',
        'number',
        'gender',
        'stem_key',
        'ending_size',
        'ending',
        'age',
        'frequency',
    ];

    public $pronoun_type = [
        'X',      // unknown, nondescript
        'PERS',   // PERSonal
        'REL',    // RELative
        'REFLEX', // REFLEXive
        'DEMONS', // DEMONStrative
        'INTERR', // INTERRogative
        'INDEF',  // INDEFinite
        'ADJECT', // ADJECTival
    ];

    public $stem_key_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * eg "SUPINE 0 0 ACC S N 4 2 um X A"
     * @var array
     */
    public $supine_attributes = [
        'which',
        'variant',
        'case',
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

    public $verb_type = [
        'X',        // all, none, or unknown
        'TO_BE',    // only the verb TO BE (esse)
        'TO_BEING', // compounds of the verb to be (esse)
        'GEN',      // verb taking the GENitive
        'DAT',      // verb taking the DATive
        'ABL',      // verb taking the ABLative
        'TRANS',    // TRANSitive verb
        'INTRANS',  // INTRANSitive verb
        'IMPERS',   // IMPERSonal verb (implied subject 'it', 'they', 'God')
                    // agent implied in action, subject in predicate
        'DEP',      // DEPonent verb
                    // only passive form but with active meaning
        'SEMIDEP',  // SEMIDEPonent verb (forms perfect as deponent)
                    // (perfect passive has active force)
        'PERFDEF',  // PERFect DEFinite verb
                    // having only perfect stem, but with present force
    ];

    public $voice_type = [
        'X',       // all, none, or unknown
        'ACTIVE',  // ACTIVE
        'PASSIVE', // PASSIVE
    ];

    public $which_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    public function __construct()
    {
        $this->numeral_value_type = range(0, 1000);
        $this->flip_properties();
    }

    public function combine_attributes_and_values($attributes, $values, $property)
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

        $attributes_count = count($attributes);
        $values_count     = count($values);

        if ($attributes_count != $values_count) {
            $message = $this->set_error_message('Inflection attributes and values do not match: %d != %d.', $attributes_count, $values_count);
            throw new Exception($message);
        }

        $attributes = array_keys($attributes);
        $inflection = array_combine($attributes, $values);

        return $inflection;
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

    public function load_inflections($lines = null)
    {
        if (! $lines) {
            $file = new file();
            $lines = $file->read_lines(__DIR__ . '/../data/INFLECTS.LAT');
        }

        $inflections = $this->parse_inflections($lines);

        return $inflections;
    }

    public function parse_inflection($line)
    {
        list($values, $part_of_speech) = $this->split_inflection($line);

        $property = $this->parts_of_speech[$part_of_speech] . '_attributes';
        $attributes = $this->$property;

        $inflection = $this->combine_attributes_and_values($attributes, $values, $property);

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

    public function split_inflection($line)
    {
        $values = preg_split('~ +~', $line, null, PREG_SPLIT_NO_EMPTY);
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
