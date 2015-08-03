<?php
class inflection
{
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

    public $decn_record = [
        'WHICH' => 0, // WHICH_TYPE := 0;
        'VAR'   => 0, // VARIANT_TYPE := 0;
    ];

    public $gender_type = [
        'X', // all, none, or unknown
        'M', // Masculine
        'F', // Feminine
        'N', // Neuter
        'C', // Common (masculine and/or feminine)
    ];

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
        'DECL'  => [ // DECN_RECORD
            'WHICH' => 0, // WHICH_TYPE
            'VAR'   => 0, // VARIANT_TYPE
        ],
        'CS'     => 'X', // CASE_TYPE
        'NUMBER' => 'X', // NUMBER_TYPE
        'GENDER' => 'X', // GENDER_TYPE
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

    public $stem_key_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

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

    public $voice_type = [
        'X',       // all, none, or unknown
        'ACTIVE',  // ACTIVE
        'PASSIVE', // PASSIVE
    ];

    public $which_type = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    public function __construct()
    {
        $this->numeral_value_type = range(0, 1000);
    }

}