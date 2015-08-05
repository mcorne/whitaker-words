<?php
require_once 'file.php';

class inflection
{
    public $adjective_inflection_keys = [
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

    public $adverb_inflection_keys = [
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

    public $conjunction_inflection_keys = [
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

    public $interjection_inflection_keys = [
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

    public $noun_inflection_keys = [
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

    public $numeral_sort_type = [
        'X',      // all, none, or unknown
        'CARD',   // CARDinal
        'ORD',    // ORDinal
        'DIST',   // DISTributive
        'ADVERB', // numeral ADVERB
    ];

    public $numeral_value_type;

    public $parts_of_speech = [
        'ADJ'    => 'adjective',
        'ADV'    => 'adverb',
        'CONJ'   => 'conjunction',
        'INTERJ' => 'interjection',
        'N'      => 'noun',
        'NUM'    => 'numeral',
        'PACK'   => 'propack',        // artificial for code
        'PREFIX' => 'prefix',         // artificial for code
        'PREP'   => 'preposition',
        'PRON'   => 'pronoun',
        'SUFFIX' => 'suffix',         // artificial for code
        'SUPINE' => 'supine',
        'TACKON' => 'tackon',         // artificial for code
        'V'      => 'verb',
        'VPAR'   => 'verb_participle',
    ];

    public $person_type = [0, 1, 2, 3];

    public $preposition_inflection_keys = [
        'case',
        'stem_key',
        'ending_size',
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

    public $verb_inflection_keys = [
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

    ///

    public $decn_inflection_keys = [
        'WHICH' => ['which_type'  , 0],
        'VAR'   => ['variant_type', 0],
    ];

    public $inflection_inflection_keys = [
        'QUAL'   => 'quality_record' ,
        'KEY'    => ['stem_key_type' , 0],
        'ENDING' => 'ending_record'  ,
        'AGE'    => ['age_type'      , 'X'],
        'FREQ'   => ['frequency_type', 'X'],
    ];

    public $numeral_inflection_keys = [
        'DECL'   => 'decn_record'       ,
        'CS'     => ['case_type'        , 'X'],
        'NUMBER' => ['number_type'      , 'X'],
        'GENDER' => ['gender_type'      , 'X'],
        'SORT'   => ['numeral_sort_type', 'X'],
    ];

    public $prefix_inflection_keys = [];

    public $pronoun_inflection_keys = [
        'which',
        'variant',
        'case',
        'number',
        'gender',
    ];

    public $propack_inflection_keys = [
        'which',
        'variant',
        'case',
        'number',
        'gender',
    ];

    public $suffix_inflection_keys = [];

    public $supine_inflection_keys = [
        'CON'              => 'decn_record' ,
        'CS'               => ['case_type'  , 'X'],
        'NUMBER'           => ['number_type', 'X'],
        'GENDER'           => ['gender_type', 'X'],
    ];

    public $tackon_inflection_keys = [];

    public $tense_voice_mood_inflection_keys = [
        'TENSE' => 'X', // TENSE_TYPE
        'VOICE' => 'X', // VOICE_TYPE
        'MOOD'  => 'X', // MOOD_TYPE
    ];

    public $vpar_inflection_keys = [
        'CON'              => 'decn_record'            ,
        'CS'               => ['case_type'             , 'X'],
        'NUMBER'           => ['number_type'           , 'X'],
        'GENDER'           => ['gender_type'           , 'X'],
        'TENSE_VOICE_MODD' => 'tense_voice_mood_record',
    ];

    public function __construct()
    {
        $this->numeral_value_type = range(0, 1000);
        $this->flip_types();
    }

    public function flip_types()
    {
        $properties = get_object_vars($this);

        foreach ($properties as $property => $values) {
            if (preg_match('~_type$~', $property)) {
                $this->$property = array_flip($values);
            }
        }
    }

    public function load_inflections()
    {
        $file = new file();
        $lines = $file->read_lines(__DIR__ . '/../data/INFLECTS.LAT');
        $inflections = $this->parse_inflections($lines);

        return $inflections;
    }

    public function parse_inflection($line, $line_number)
    {

        list($inflection_values, $part_of_speech) = $this->split_inflection($line, $line_number);

        $inflection_keys_property = $this->parts_of_speech[$part_of_speech] . '_inflection_keys';
        $inflection_keys = $this->$inflection_keys_property;

        $value_count = count($inflection_values);

        $parsed['part_of_speech'] = $part_of_speech;

        foreach ($inflection_values as $index => $inflection_value) {
            if (! isset($inflection_keys[$index])) {
                throw new Exception("Error line #$line_number! Unexpected value: $inflection_value.");
            }

            $inflection_key = $inflection_keys[$index];
            $ending_size = empty($parsed['ending_size']) ? 0 : $parsed['ending_size'];
            list($inflection_value, $value_count) = $this->validate_inflection_value($inflection_key, $inflection_value, $ending_size, $value_count, $line_number);
            $parsed[$inflection_key] = $inflection_value;
        }

        $this->validate_inflection_value_count($inflection_keys, $value_count, $line_number);

        $parsed['line_number'] = $line_number;

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

            $line_number = $index + 1;
            $inflections[] = $this->parse_inflection($line, $line_number);
            break; // TODO: remove
        }

        return $inflections;
    }

    public function split_inflection($line, $line_number)
    {
        $inflection_values = preg_split('~ +~', $line, null, PREG_SPLIT_NO_EMPTY);
        $part_of_speech = array_shift($inflection_values);

        if (! isset($this->parts_of_speech[$part_of_speech])) {
            throw new Exception("Error line #$line_number! Invalid part of speech: $part_of_speech.");
        }

        return [$inflection_values, $part_of_speech];
    }

    public function validate_inflection_value($inflection_key, $inflection_value, $ending_size, $value_count, $line_number)
    {
        if ($inflection_key == 'ending') {
            if (empty($ending_size)) {
                $inflection_value = null;
                $value_count++;

            } elseif ($ending_size != strlen($inflection_value)) {
                throw new Exception("Error line #$line_number! Ending and size do not match.");
            }

        } else {
            $property = $inflection_key . '_type';

            if (! isset($this->$property)) {
                throw new Exception("Invalid property: $property");
            }

            $valid_inflection_values = $this->$property;

            if (!isset($valid_inflection_values[$inflection_value])) {
                throw new Exception("Error line #$line_number! Invalid key value: $inflection_key => $inflection_value.");
            }
        }

        return [$inflection_value, $value_count];
    }

    public function validate_inflection_value_count($inflection_keys, $value_count, $line_number)
    {
        $missing_count = count($inflection_keys) - $value_count;

        if ($missing_count > 0) {
            throw new Exception("Error line #$line_number! $missing_count missing value(s).");
        }

        if ($missing_count < 0) {
            $missing_count = -$missing_count;
            throw new Exception("Error line #$line_number! $missing_count unexpected values(s).");
        }
    }
}
