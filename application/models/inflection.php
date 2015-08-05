<?php
require_once 'file.php';

class inflection
{
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

    public $comparison_type = [
        'X',     // all, none, or unknown
        'POS',   // POSitive
        'COMP',  // COMParative
        'SUPER', // SUPERlative
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

    public $stem_key_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    ///

    public $adjective_record = [
        'DECL'   => 'decn_record'     ,
        'CS'     => ['case_type'      , 'X'],
        'NUMBER' => ['number_type'    , 'X'],
        'GENDER' => ['gender_type'    , 'X'],
        'CO'     => ['comparison_type', 'X'],
    ];

    public $adverb_record = [
        'CO'     => ['comparison_type', 'X'],
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

    public $conjunction_record = [];

    public $decn_record = [
        'WHICH' => ['which_type'  , 0],
        'VAR'   => ['variant_type', 0],
    ];

    public $gender_type = [
        'X', // all, none, or unknown
        'M', // Masculine
        'F', // Feminine
        'N', // Neuter
        'C', // Common (masculine and/or feminine)
    ];

    public $inflection_record = [
        'QUAL'   => 'quality_record' ,
        'KEY'    => ['stem_key_type' , 0],
        'ENDING' => 'ending_record'  ,
        'AGE'    => ['age_type'      , 'X'],
        'FREQ'   => ['frequency_type', 'X'],
    ];

    public $interjection_record = [];

    public $mood_type = [
        'X',   // all, none, or unknown
        'IND', // INDicative
        'SUB', // SUBjunctive
        'IMP', // IMPerative
        'INF', // INFinative
        'PPL', // ParticiPLe
    ];

    public $noun_kind_type = [
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

    public $noun_record = [
        'DECL'   => 'decn_record' ,
        'CS'     => ['case_type'  , 'X'],
        'NUMBER' => ['number_type', 'X'],
        'GENDER' => ['gender_type', 'X'],
    ];

    public $number_type = [
        'X', // all, none, or unknown
        'S', // Singular
        'P', // Plural
    ];

    public $numeral_record = [
        'DECL'   => 'decn_record'       ,
        'CS'     => ['case_type'        , 'X'],
        'NUMBER' => ['number_type'      , 'X'],
        'GENDER' => ['gender_type'      , 'X'],
        'SORT'   => ['numeral_sort_type', 'X'],
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
        'PACK'   => 'propack',
        'PREFIX' => 'prefix',
        'PREP'   => 'preposition',
        'PRON'   => 'pronoun',
        'SUFFIX' => 'suffix',
        'SUPINE' => 'supine',
        'TACKON' => 'tackon',
        'V'      => 'verb',
        'VPAR'   => 'vpar',
    ];

    public $part_of_speech_type = [
        'X',      // all, none, or unknown
        'N',      // Noun
        'PRON',   // PRONoun
        'PACK',   // PACKON -- artificial for code
        'ADJ',    // ADJective
        'NUM',    // NUMeral
        'ADV',    // ADVerb
        'V',      // Verb
        'VPAR',   // Verb PARticiple
        'SUPINE', // SUPINE
        'PREP',   // PREPosition
        'CONJ',   // CONJunction
        'INTERJ', // INTERJection
        'TACKON', // TACKON -- artificial for code
        'PREFIX', // PREFIX -- here artificial for code
        'SUFFIX', // SUFFIX -- here artificial for code
    ];

    public $person_type = [0, 1, 2, 3];

    public $prefix_record = [];

    public $preposition_record = [
        'OBJ' => ['case_type', 'X'],
    ];

    public $pronoun_kind_type = [
        'X',      // unknown, nondescript
        'PERS',   // PERSonal
        'REL',    // RELative
        'REFLEX', // REFLEXive
        'DEMONS', // DEMONStrative
        'INTERR', // INTERRogative
        'INDEF',  // INDEFinite
        'ADJECT', // ADJECTival
    ];

    public $pronoun_record = [
        'DECL'   => 'decn_record' ,
        'CS'     => ['case_type'  , 'X'],
        'NUMBER' => ['number_type', 'X'],
        'GENDER' => ['gender_type', 'X'],
    ];

    public $propack_record = [
        'DECL'   => 'decn_record' ,
        'CS'     => ['case_type'  , 'X'],
        'NUMBER' => ['number_type', 'X'],
        'GENDER' => ['gender_type', 'X'],
    ];

    public $quality_record_case = [
        'ADJ'    => 'adjective_record',
        'ADV'    => 'adverb_record',
        'CONJ'   => 'conjunction_record',
        'INTERJ' => 'interjection_record',
        'N'      => 'noun_record',
        'NUM'    => 'numeral_record',
        'PACK'   => 'propack_record',
        'PREFIX' => 'prefix_record',
        'PREP'   => 'preposition_record',
        'PRON'   => 'pronoun_record',
        'SUFFIX' => 'suffix_record',
        'SUPINE' => 'supine_record',
        'TACKON' => 'tackon_record',
        'V'      => 'verb_record',
        'VPAR'   => 'vpar_record',
    ];

    public $suffix_record = [];

    public $supine_record = [
        'CON'              => 'decn_record' ,
        'CS'               => ['case_type'  , 'X'],
        'NUMBER'           => ['number_type', 'X'],
        'GENDER'           => ['gender_type', 'X'],
    ];

    public $tackon_record = [];

    public $tense_voice_mood_record = [
        'TENSE' => 'X', // TENSE_TYPE
        'VOICE' => 'X', // VOICE_TYPE
        'MOOD'  => 'X', // MOOD_TYPE
    ];

    public $variant_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    public $verb_kind_type = [
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

    public $verb_record = [
        'CON'              => 'decn_record'            ,
        'TENSE_VOICE_MOOD' => 'tense_voice_mood_record',
        'PERSON'           => ['person_type'           , 0],
        'NUMBER'           => ['number_type'           , 'X'],
    ];

    public $voice_type = [
        'X',       // all, none, or unknown
        'ACTIVE',  // ACTIVE
        'PASSIVE', // PASSIVE
    ];

    public $vpar_record = [
        'CON'              => 'decn_record'            ,
        'CS'               => ['case_type'             , 'X'],
        'NUMBER'           => ['number_type'           , 'X'],
        'GENDER'           => ['gender_type'           , 'X'],
        'TENSE_VOICE_MODD' => 'tense_voice_mood_record',
    ];

    public $which_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

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

    public function parse_inflection($line, $number)
    {
        $inflection_values = preg_split('~ +~', $line, null, PREG_SPLIT_NO_EMPTY);
        $part_of_speech = array_shift($inflection_values);

        if (! isset($this->parts_of_speech[$part_of_speech])) {
            throw new Exception("Error line #$number! Invalid part of speech: $part_of_speech.");
        }

        $inflection_keys = $this->parts_of_speech[$part_of_speech] . '_inflection_keys';

        if (count($inflection_values) != count($this->$inflection_keys)) {
            throw new Exception("Error line #$number! Inflection keys and values do not match.");
        }

        $parsed['part_of_speech'] = $part_of_speech;

        foreach ($this->$inflection_keys as $index => $inflection_key) {
            $property = $inflection_key . '_type';

            if (! isset($this->$property)) {
                throw new Exception("Invalid property: $property");
            }

            $inflection_value = $inflection_values[$index];

            if (! array_key_exists($inflection_value, $this->$property)) {
                throw new Exception("Error line #$number! Invalid key value: $inflection_key => $inflection_value.");
            }

            $parsed[$inflection_key] = $inflection_value;
        }

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

            $number = $index + 1;
            $inflection = $this->parse_inflection($line, $number);
        }

        return $inflection;
    }

    public function load_inflections()
    {
        $file = new file();
        $lines = $file->read_lines(__DIR__ . '/../data/INFLECTS.LAT');
        $inflections = $this->parse_inflections($lines);
    }
}