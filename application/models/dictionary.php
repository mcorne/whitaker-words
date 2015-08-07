<?php
require_once 'common.php';

class dictionary extends common
{
    const MEANING_POSITION        = 110;
    const PART_OF_SPEECH_POSITION = 76;

    /**
     * eg "abact abact                      ADJ 1 1 POS X X X E S driven away/off/back;"
     * eg "abject abject abjecti abjectissi ADJ 1 1 X   X X X B L downcast, dejected;"
     * @var array
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
     * eg "abdicative                     ADV POS D X X E S negatively;"
     * eg "abjecte abjectius abjectissime ADV X   X X X C L in spiritless manner;"
     * @var array
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
     * eg "ac CONJ X X X A O and, and also, and besides;"
     * @var array
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
     * eg "aelinon INTERJ X X X F O exclamation of sorrow;"
     * @var array
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
     * eg "abac abac N 2 1 M T E E X C E small table for cruets, credence;"
     * eg "Act       N 9 8 N T E E X D E Acts (abbreviation);"
     * @var array
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
     * eg "amb                                     NUM 1 2 CARD 0   X X X B O both; two of pair;"
     * eg "biscentum biscentesim biscenten biscent NUM 2 0 X    200 X X X C E two hundred;"
     * @var array
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

    public $numeral_value_type;

    /**
     * eg "qu cu PACK 1 0 REL X X X A X (w/-cumque) who/whatever;"
     * @var array
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
     * eg "ab PREP ABL X X X A O by (agent), from;"
     * @var array
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
     * eg "aliqu alicu PRON 1 0 INDEF X X X A O anyone/anybody/anything;"
     * @var array
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
     * Note on other sources:
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
     * eg "abaestu abaestu abaestuav abaestuat V 1 1 INTRANS D X X F S wave down;"
     * eg "cumi                                V 9 9 X       E E Q F E arise;"
     * @var array
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

    public function __construct()
    {
        $this->numeral_value_type = range(0, 1000);
        parent::__construct();
    }

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

    public function extract_meaning($line)
    {
        $meaning = substr($line, self::MEANING_POSITION);

        if (! $meaning = trim($meaning)) {
            $message = $this->set_error_message('No meaning.');
            throw new Exception($message);
        }

        return $meaning;
    }

    public function extract_other_attributes($line)
    {
        $line_part = substr($line, self::PART_OF_SPEECH_POSITION, self::MEANING_POSITION - self::PART_OF_SPEECH_POSITION);
        $other_attributes = preg_split('~ +~', $line_part, null, PREG_SPLIT_NO_EMPTY);

        return $other_attributes;
    }

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

    public function extract_part_of_speech($line)
    {
        $line_without_stems = substr($line, self::PART_OF_SPEECH_POSITION);
        list($part_of_speech) = explode(' ', $line_without_stems);
        $this->validate_part_of_speech($part_of_speech);

        return $part_of_speech;
    }

    public function load_dictionary($lines = null)
    {
        if (! $lines) {
            $lines = $this->read_lines(__DIR__ . '/../data/DICTLINE.GEN');
        }

        $entries = $this->parse_entries($lines);
        // $this->create_dictionary_table();
        // $this->insert_entries($entries);

        return $entries;
    }

    public function parse_entry($line)
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
        }

        $this->validate_stem_count($entry);

        $entry['line_number'] = $this->line_number;

        return $entry;
    }

    public function split_entry($line)
    {
        $values = array_merge($this->extract_stems($line), $this->extract_other_attributes($line));
        $values[] = $this->extract_meaning($line);

        $this->validate_unique_entry($values);

        return $values;
    }

    public function validate_stem_count($entry)
    {
        switch ($entry['part_of_speech']) {
            case 'ADJ':
                $expected_stem_count = $entry['comparison'] == 'X' ? 4 : 2;
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