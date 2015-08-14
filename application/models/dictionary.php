<?php
require_once 'common.php';

/**
 * Parsing of the dictionary file and loading of the entries in the database
 */
class dictionary extends common
{
    /**
     * Position of the meaning attribute in a dictionary entry line
     */
    const MEANING_POSITION        = 110;

    /*
     * Position of the part of speech in a dictionary entry line
     */
    const PART_OF_SPEECH_POSITION = 76;

    /**
     * The adjective attributes
     *
     * eg "abact abact                      ADJ 1 1 POS   X X X E S driven away/off/back;"
     * eg "abject abject abjecti abjectissi ADJ 1 1 X     X X X B L downcast, dejected;"
     * eg "adinstar                         ADJ 9 9 POS   X X X E S like, after the fashion of;"
     * eg "adpri                            ADJ 0 0 SUPER X X X E O very first, most excellent;"
     * @var array
     * @source source/dictionary_package.ads ADJECTIVE_ENTRY
     */
    public $adjective_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'which',
        'variant',
        'comparison',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * The adverb attributes
     *
     * eg "abdicative                     ADV POS D X X E S negatively;"
     * eg "abjecte abjectius abjectissime ADV X   X X X C L in spiritless manner;"
     * @var array
     * @source source/dictionary_package.ads ADVERB_ENTRY
     */
    public $adverb_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'comparison',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * List of areas or domains
     *
     * @var array
     * @source source/dictionary_package.ads AREA_TYPE
     */
    public $area_type = [
        'X', // All or none
        'A', // Agriculture, Flora, Fauna, Land, Equipment, Rural
        'B', // Biological, Medical, Body Parts
        'D', // Drama, Music, Theater, Art, Painting, Sculpture
        'E', // Ecclesiastic, Biblical, Religious
        'G', // Grammar, Retoric, Logic, Literature, Schools
        'L', // Legal, Government, Tax, Financial, Political, Titles
        'P', // Poetic
        'S', // Science, Philosophy, Mathematics, Units/Measures
        'T', // Technical, Architecture, Topography, Surveying
        'W', // War, Military, Naval, Ships, Armor
        'Y', // Mythology
    ];

    /**
     * The conjunction attributes
     *
     * eg "ac CONJ X X X A O and, and also, and besides;"
     * @var array
     * @source source/dictionary_package.ads CONJUNCTION_ENTRY
     */
    public $conjunction_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * List of geographies or locations
     *
     * @var array
     * @source source/dictionary_package.ads GEO_TYPE
     */
    public $geography_type = [
        'X', // All or none
        'A', // Africa
        'B', // Britian
        'C', // China
        'D', // Scandinavia
        'E', // Egypt
        'F', // France, Gaul
        'G', // Germany
        'H', // Greece
        'I', // Italy, Rome
        'J', // India
        'K', // Balkans
        'N', // Netherlands
        'P', // Persia
        'Q', // Near East
        'R', // Russia
        'S', // Spain, Iberia
        'U', // Eastern Europe
    ];

    /**
     * The interjection attributes
     *
     * eg "aelinon INTERJ X X X F O exclamation of sorrow;"
     * @var array
     * @source source/dictionary_package.ads INTERJECTION_ENTRY
     */
    public $interjection_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * The noun attributes
     *
     * eg "abac abac N 2 1 M T E E X C E small table for cruets, credence;"
     * eg "Act       N 9 8 N T E E X D E Acts (abbreviation);"
     * @var array
     * @source source/dictionary_package.ads NOUN_ENTRY
     */
    public $noun_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'which',
        'variant',
        'gender',
        'noun_kind',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * List of noun kinds
     *
     * @var array
     * @source source/dictionary_package.ads NOUN_KIND_TYPE
     */
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

    /**
     * The numeral attributes
     *
     * eg "amb                                     NUM 1 2 CARD 0   X X X B O both; two of pair;"
     * eg "biscentum biscentesim biscenten biscent NUM 2 0 X    200 X X X C E two hundred;"
     * @var array
     * @source source/dictionary_package.ads NUMERAL_ENTRY
     */
    public $numeral_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'which',
        'variant',
        'numeral_sort',
        'numeral_value',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * List of numeral values
     *
     * @var array
     * @see self::__constructor()
     * @source source/inflections_package.ads NUMERAL_VALUE_TYPE
     */
    public $numeral_value_type;

    /**
     * The packon attributes
     *
     * eg "qu cu PACK 1 0 REL X X X A X (w/-cumque) who/whatever;"
     * @var array
     * @source source/dictionary_package.ads PROPACK_ENTRY
     */
    public $packon_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'which',
        'variant',
        'pronoun_kind',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
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
        'PACK'   => 'packon',
        'PREP'   => 'preposition',
        'PRON'   => 'pronoun',
        'V'      => 'verb',
    ];

    /**
     * The preposition attributes
     *
     * eg "ab PREP ABL X X X A O by (agent), from;"
     * @var array
     * @source source/dictionary_package.ads PREPOSITION_ENTRY
     */
    public $preposition_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'cases',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * The pronoun attributes
     *
     * eg "aliqu alicu PRON 1 0 INDEF X X X A O anyone/anybody/anything;"
     * @var array
     * @source source/dictionary_package.ads PRONOUN_ENTRY
     */
    public $pronoun_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'which',
        'variant',
        'pronoun_kind',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * List of pronoun kinds
     *
     * @var array
     * @source source/dictionary_package.ads PRONOUN_KIND_TYPE
     */
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

    /**
     * List of sources
     *
     * Original note (Whitaker's) on other sources:
     * Consulted but used only indirectly
     * Liddell + Scott Greek-English Lexicon (Lid)
     * Oxford English Dictionary 2002 (OED)
     *
     * Consulted but used only occasionally, seperately referenced
     * D.A. Kidd, Collins Latin Gem Dictionary, 1957 (Col)
     * Allen + Greenough, New Latin Grammar, 1888 (A+G)
     * Harrington/Pucci/Elliott, Medieval Latin 2nd Ed 1997 (Harr)
     * C.C./C.L. Scanlon Latin Grammar/Second Latin, TAN 1976 (SCANLON)
     * W. M. Lindsay, Short Historical Latin Grammar, 1895 (Lindsay)
     * Du Cange
     * Oxford English Dictionary (OED)
     *
     * Note that the WORDS dictionary is not just a copy of source info, but the
     * indicated SOURCE is a main reference/check point used to derive the entry
     *
     * @var array
     * @source source/dictionary_package.ads SOURCE_TYPE
     */
    public $source_type = [
        'X', // General or unknown or too common to say
        'A',
        'B', // C.H.Beeson, A Primer of Medieval Latin, 1925 (Bee)
        'C', // Charles Beard, Cassell's Latin Dictionary 1892 (Cas)
        'D', // J.N.Adams, Latin Sexual Vocabulary, 1982 (Sex)
        'E', // L.F.Stelten, Dictionary of Eccles. Latin, 1995 (Ecc)
        'F', // Roy J. Deferrari, Dictionary of St. Thomas Aquinas, 1960 (DeF)
        'G', // Gildersleeve + Lodge, Latin Grammar 1895 (G+L)
        'H', // Collatinus Dictionary by Yves Ouvrard
        'I', // Leverett, F.P., Lexicon of the Latin Language, Boston 1845
        'J', // Bracton: De Legibus Et Consuetudinibus Angliae
        'K', // Calepinus Novus, modern Latin, by Guy Licoppe (Cal)
        'L', // Lewis, C.S., Elementary Latin Dictionary 1891
        'M', // Latham, Revised Medieval Word List, 1980 (Latham)
        'N', // Lynn Nelson, Wordlist (Nel)
        'O', // Oxford Latin Dictionary, 1982 (OLD)
        'P', // Souter, A Glossary of Later Latin to 600 A.D., Oxford 1949 (Souter)
        'Q', // Other, cited or unspecified dictionaries
        'R', // Plater + White, A Grammar of the Vulgate, Oxford 1926 (Plater)
        'S', // Lewis and Short, A Latin Dictionary, 1879 (L+S)
        'T', // Found in a translation  --  no dictionary reference
        'U', //
        'V', // Vademecum in opus Saxonis - Franz Blatt (Saxo)
        'W', // My personal guess, mostly obvious extrapolation (Whitaker or W)
        'Y', // Temp special code
        'Z', // Sent by user --  no dictionary reference
             // Mostly John White of Blitz Latin
    ];

    /**
     * The dictionary entry table definition
     *
     * string type
     */
    public $sql_table = '
        DROP TABLE IF EXISTS dictionary;
        VACUUM;

        CREATE TABLE dictionary (
        id             INTEGER PRIMARY KEY,
        stem1          TEXT NOT NULL,
        stem2          TEXT,
        stem3          TEXT,
        stem4          TEXT,
        part_of_speech TEXT NOT NULL,
        which          INTEGER,
        variant        INTEGER,
        comparison     TEXT,
        gender         TEXT,
        noun_kind      TEXT,
        numeral_sort   TEXT,
        numeral_value  TEXT,
        pronoun_kind   TEXT,
        cases          TEXT,
        verb_kind      TEXT,
        age            TEXT NOT NULL,
        area           TEXT NOT NULL,
        geography      TEXT NOT NULL,
        frequency      TEXT NOT NULL,
        source         TEXT NOT NULL,
        meaning        TEXT NOT NULL,
        line_number    INTEGER NOT NULL);
    ';

    /**
     * The dictionary views and indexes definition
     *
     * @var string
     */
    public $sql_views_and_indexes = '
        DROP VIEW IF EXISTS entries_by_part_of_speech;
        CREATE VIEW entries_by_part_of_speech AS
        SELECT
            part_of_speech,
            count(part_of_speech) AS count
        FROM dictionary group by part_of_speech
        UNION
        SELECT
            "-- Total --" AS part_of_speech,
            count(*) AS count
        FROM dictionary;
    ';

    /**
     * Dictionary entry parsing tests
     *
     * @var array
     */
    public $test_lines = [
        'abact              abact                                                    ADJ    1 1 POS          X X X E S driven away/off/back;',
        'abject             abject             abjecti            abjectissi         ADJ    1 1 X            X X X B L downcast, dejected;',
        'abdicative                                                                  ADV    POS              D X X E S negatively;',
        'abjecte            abjectius          abjectissime                          ADV    X                X X X C L in spiritless manner;',
        'ac                                                                          CONJ                    X X X A O and, and also, and besides;',
        'aelinon                                                                     INTERJ                  X X X F O exclamation of sorrow;',
        'abac               abac                                                     N      2 1 M T          E E X C E small table for cruets;',
        'Act                                                                         N      9 8 N T          E E X D E Acts (abbreviation);',
        'amb                                                                         NUM    1 2 CARD       0 X X X B O both; two of pair;',
        'biscentum          biscentesim        biscenten          biscent            NUM    2 0 X        200 X X X C E two hundred;',
        'qu                 cu                                                       PACK   1 0 REL          X X X A X (w/-cumque) who/whatever;',
        'ab                                                                          PREP   ABL              X X X A O by (agent), from;',
        'aliqu              alicu                                                    PRON   1 0 INDEF        X X X A O anyone/anybody/anything;',
        'abaestu            abaestu            abaestuav          abaestuat          V      1 1 INTRANS      D X X F S wave down;',
        'cumi                                                                        V      9 9 X            E E Q F E arise;',
    ];

    /**
     * The verb attributes
     *
     * eg "abaestu abaestu abaestuav abaestuat V 1 1 INTRANS D X X F S wave down;"
     * eg "cumi                                V 9 9 X       E E Q F E arise;"
     * @var array
     * @source source/dictionary_package.ads VERB_ENTRY
     */
    public $verb_attributes = [
        'stem1',
        'stem2',
        'stem3',
        'stem4',
        'part_of_speech',
        'which',
        'variant',
        'verb_kind',
        'age',
        'area',
        'geography',
        'frequency',
        'source',
        'meaning',
    ];

    /**
     * List of verb kinds
     *
     * @var array
     * @source source/dictionary_package.ads VERB_KIND_TYPE
     */
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

    /**
     * Sets numeral values, and flips attribute and type properties
     */
    public function __construct()
    {
        parent::__construct();

        $this->numeral_value_type = range(0, 1000);
        $this->flip_properties();
    }

    /**
     * Combines the dictionary entry attributes and values
     *
     *
     * Removes unused stems.
     * Note that all stems are added in all item attributes for simplicity.
     *
     * @param array $attributes
     * @param array $values
     * @return array the dictionary entry
     */
    public function combine_entry_attributes_and_values($attributes, $values)
    {
        $entry = $this->combine_attributes_and_values($attributes, $values);

        if (is_null($entry['stem2'])) {
            unset($entry['stem2']);
        }
        if (is_null($entry['stem3'])) {
            unset($entry['stem3']);
        }
        if (is_null($entry['stem4'])) {
            unset($entry['stem4']);
        }

        return $entry;
    }

    /**
     * Returns the dictionary entry meaning
     *
     * @param string $line
     * @return string the meaning
     * @throws Exception
     */
    public function extract_meaning($line)
    {
        $meaning = substr($line, self::MEANING_POSITION);

        if (! $meaning = trim($meaning)) {
            $message = $this->set_error_message('No meaning.');
            throw new Exception($message);
        }

        return $meaning;
    }

    /**
     * Returns the entry attributes excluding the stems and meaning
     *
     * @param string $line
     * @return array the attributes
     */
    public function extract_other_attributes($line)
    {
        $line_part = substr($line, self::PART_OF_SPEECH_POSITION, self::MEANING_POSITION - self::PART_OF_SPEECH_POSITION);
        $other_attributes = preg_split('~ +~', $line_part, null, PREG_SPLIT_NO_EMPTY);

        return $other_attributes;
    }

    /**
     * Returns the part of speech
     *
     * @param type $line
     * @return string the part of speech
     */
    public function extract_part_of_speech($line)
    {
        $line_without_stems = substr($line, self::PART_OF_SPEECH_POSITION);
        list($part_of_speech) = explode(' ', $line_without_stems);
        $this->validate_part_of_speech($part_of_speech);

        return $part_of_speech;
    }

    /**
     * Returns the stems
     *
     * @param string $line
     * @return array the stems
     * @throws Exception
     */
    public function extract_stems($line)
    {
        $line_part = substr($line, 0, self::PART_OF_SPEECH_POSITION);

        if (! $stems = preg_split('~ +~', $line_part, null, PREG_SPLIT_NO_EMPTY)) {
            $message = $this->set_error_message('No stem.');
            throw new Exception($message);
        }

        $stems = array_pad($stems, 4, null);

        return $stems;
    }

    /**
     * Reads, parses and loads the dictionary entries into the database
     *
     * @return int the number of dictionary entries
     */
    public function load_dictionary()
    {
        $lines = $this->read_lines(__DIR__ . '/../data/DICTLINE.GEN');

        $entries = $this->parse_entries($lines);
        $count = $this->load_table('dictionary', $entries);

        return $count;
    }

    /**
     * Parses a dictionary entry
     *
     * @param string $line
     * @param int $entry_id
     * @return array the dictionary entry
     */
    public function parse_entry($line, $entry_id)
    {
        $part_of_speech = $this->extract_part_of_speech($line);
        $values = $this->split_entry($line);

        $property = $this->parts_of_speech[$part_of_speech] . '_attributes';
        $attributes = $this->$property;

        $entry = $this->combine_entry_attributes_and_values($attributes, $values);

        foreach ($entry as $attribute => $value) {
            if (! in_array($attribute, ['stem1', 'stem2', 'stem3', 'stem4', 'part_of_speech', 'meaning'])) {
                $this->validate_entry_value($attribute, $value);
            }
            // else: the stems are validated below, the part of speech and meaning are already validated at this point
        }

        $this->validate_stem_count($entry);

        $entry['line_number'] = $this->line_number;
        $entry['id'] = $entry_id;

        return $entry;
    }

    /**
     * Splits the dictionary entry line into values
     *
     * @param string $line
     * @return array the dictionary entry values
     */
    public function split_entry($line)
    {
        $values = array_merge($this->extract_stems($line), $this->extract_other_attributes($line));
        $values[] = $this->extract_meaning($line);

        $this->validate_unique_entry($values);

        return $values;
    }

    /**
     * Verifies the number of stems is consistent with the part of speech
     *
     * @param array $entry
     * @throws Exception
     */
    public function validate_stem_count($entry)
    {
        switch ($entry['part_of_speech']) {
            case 'ADJ':
                if ($entry['comparison'] == 'X') {
                    $expected_stem_count = 4;
                } elseif ($entry['which'] == 9 or $entry['which'] == 0) {
                    $expected_stem_count = 1;
                } else {
                    $expected_stem_count = 2;
                }
                break;

            case 'ADV':
                $expected_stem_count = $entry['comparison'] == 'X' ? 3 : 1;
                break;

            case 'CONJ':
            case 'INTERJ':
            case 'PREP':
                $expected_stem_count = 1;
                break;

            case 'N':
                $expected_stem_count = $entry['which'] == 9 ? 1 : 2;
                break;

            case 'NUM':
                $expected_stem_count = $entry['numeral_sort'] == 'X' ? 4 : 1;
                break;

            case 'PACK':
            case 'PRON':
                $expected_stem_count = 2;
                break;

            case 'V':
                $expected_stem_count = $entry['which'] == 9 ? 1 : 4;
                break;

            default:
                throw new Exception('Unhandled part of speech: ' . $entry['part_of_speech']);
        }

        $stem_count = 1;
        $stem_count += (int) isset($entry['stem2']);
        $stem_count += (int) isset($entry['stem3']);
        $stem_count += (int) isset($entry['stem4']);

        if ($stem_count != $expected_stem_count) {
            $message = $this->set_error_message("Bad stem count: %d != %d", $stem_count, $expected_stem_count);
            throw new Exception($message);
        }
    }
}
