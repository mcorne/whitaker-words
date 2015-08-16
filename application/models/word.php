<?php
require_once 'common.php';

/**
 * Creation and loading of all the inflected words in the database
 */
class word extends common
{
    /**
     * Inflections cache
     *
     * @var array
     * @see self::get_inflections()
     */
    public $inflections;

    /**
     * Inflection attributes used in dictionary entries
     *
     * @var array
     * @see self::$sql_selects, the vsprintf() args order must be the same in both arrays
     * @see self::__construct(), the attributes are flipped for conveniance
     */
    public $inflection_attributes = [
        'ADJ'    => ['which', 'variant', 'comparison'],
        'ADV'    => ['comparison'],
        'CONJ'   => [],
        'INTERJ' => [],
        'N'      => ['which', 'variant', 'gender'],
        'NUM'    => ['which', 'variant', 'numeral_sort'],
        'PREP'   => ['cases'],
        'PRON'   => ['which', 'variant'],
        'SUPINE' => ['which'],
        'V'      => ['which', 'variant'],
        'VPAR'   => ['which', 'variant'],
    ];

    /**
     * The part of speech for which the inflected words are created
     *
     * The default is null, meaning all parts of speech.
     * This feature is only used for development purposes.
     *
     * @var string
     */
    public $part_of_speech;

    /**
     * The select statements to extract inflection details including the id, ending and stem key
     *
     * @var array
     * @see self::$inflection_attributes, the vsprintf() args order must be the same in both arrays
     * @see inflection::$sql_views_and_indexes, the select statements leverage indexes
     */
    public $sql_selects = [
        'ADJ'    => '
            SELECT * FROM inflection
            WHERE part_of_speech = "ADJ"
            AND (which = %1$d OR which = 0)
            AND (variant = %2$d OR variant = 0)
            AND (comparison = "%3$s" OR "%3$s" = "X")
        ',
        'ADV'    => '
            SELECT * FROM inflection
            WHERE part_of_speech = "ADV"
            AND comparison = "%s"
        ',
        'CONJ'   => '
            SELECT * FROM inflection
            WHERE part_of_speech = "CONJ"
        ',
        'INTERJ' => '
            SELECT * FROM inflection
            WHERE part_of_speech = "INTERJ"
        ',
        'N'      => '
            SELECT * FROM inflection
            WHERE part_of_speech = "N"
            AND which = %1$d
            AND (variant = %2$d OR variant = 0)
            AND (gender = "%3$s" OR gender = "C" OR gender = "X")
        ',
        'NUM'    => '
            SELECT * FROM inflection
            WHERE part_of_speech = "NUM"
            AND (which = %1$d OR which = 0)
            AND (variant = %2$d OR variant = 0)
            AND (numeral_sort = "%3$s" OR "%3$s" = "X")
        ',
        'PREP'   => '
            SELECT * FROM inflection
            WHERE part_of_speech = "PREP"
            AND cases = "%s"
        ',
        'PRON'   => '
            SELECT * FROM inflection
            WHERE part_of_speech = "PRON"
            AND which = %1$d
            AND (variant = %2$d OR variant = 0)
        ',
        'SUPINE' => '
            SELECT * FROM inflection
            WHERE part_of_speech = "SUPINE"
            AND %1$d != 9
        ',
        'V'      => '
            SELECT * FROM inflection
            WHERE part_of_speech = "V"
            AND (which = %1$d OR which = 0 AND %1$d != 9)
            AND (variant = %2$d OR variant = 0)
        ',
        'VPAR'   => '
            SELECT * FROM inflection
            WHERE part_of_speech = "VPAR"
            AND (which = %1$d OR which = 0 AND %1$d != 9)
            AND (variant = %2$d OR variant = 0)
        ',
    ];

    /**
     * The inflected words table definition
     *
     * @var string
     */
    public $sql_table = '
        DROP TABLE IF EXISTS word;
        VACUUM;

        CREATE TABLE word (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        entry_id      INTEGER,
        inflection_id INTEGER,
        word          TEXT);
    ';

    /**
     * The inflected words views and indexes definition
     *
     * @var string
     */
    public $sql_views_and_indexes = '
        DROP INDEX IF EXISTS word_word;
        CREATE INDEX word_word ON word (word);

        DROP VIEW IF EXISTS count_words;
        CREATE VIEW count_words AS
        SELECT
            inflection.part_of_speech,
            count(inflection.part_of_speech) AS count
        FROM word
        JOIN inflection ON inflection.id = word.inflection_id
        GROUP BY part_of_speech
        UNION
        SELECT
            "-- Total --" AS part_of_speech,
            count(*) AS count
        FROM word;
    ';

    /**
     * Flips the inflection attributes
     */
    public function __construct()
    {
        parent::__construct();

        $this->inflection_attributes = array_map('array_flip', $this->inflection_attributes);
    }

    /**
     * Adds all possible endings to the dictionary entry
     *
     * @param array $inflections
     * @param array $entry
     * @return array the entry inflections
     * @throws Exception
     */
    public function add_endings($inflections, $entry)
    {
        $words = [];

        foreach ($inflections as $inflection) {
            $stem = $this->get_stem($inflection['stem_key'], $entry, $inflection['id']);

            if (! $stem) {
                throw new Exception("Empty stem or invalid stem key: {$inflection['stem_key']} in entry id: {$entry['id']}");
            }

            if ($stem == 'zzz' or ! $this->is_valid_inflection($inflection, $entry)) {
                // this is a stem not to be used or not a valid inflection, ignores the inflection
                continue;
            }

            $words[] = [
                'entry_id'      => $entry['id'],
                'inflection_id' => $inflection['id'],
                'word'          => $stem . $inflection['ending'],
            ];
        }

        return $words;
    }

    /**
     * Extracts the entry attributes to be used to get the possible inflections
     *
     * @param string $part_of_speech
     * @param array $entry
     * @return array a sub set of the entry attributes
     * @throws Exception
     */
    public function extract_attributes($part_of_speech, $entry)
    {
        if (! isset($this->inflection_attributes[$part_of_speech])) {
            throw new Exception("Unavailable inflection attributes for: $part_of_speech");
        }

        $attributes = array_intersect_key($entry, $this->inflection_attributes[$part_of_speech]);

        return $attributes;
    }

    /**
     * Fixes the entry
     *
     * Sets the appropriate stem for some inflection.
     *
     * @param array $entry
     * @return array the entry fixed
     */
    public function fix_entry($entry)
    {
        if ($entry['part_of_speech'] == 'ADJ') {
            if ($entry['which'] == 0) {
                if ($entry['variant'] == 0) {
                    if ($entry['comparison'] == 'COMP') {
                        $entry['stem3'] = $entry['stem1'];
                        $entry['stem1'] = null;

                    } elseif ($entry['comparison'] == 'SUPER') {
                        $entry['stem4'] = $entry['stem1'];
                        $entry['stem1'] = null;
                    }
                }
            }

        } elseif ($entry['part_of_speech'] == 'NUM') {
            if ($entry['numeral_sort'] == 'ORD') {
                $entry['stem2'] = $entry['stem1'];
                $entry['stem1'] = null;

            } elseif ($entry['numeral_sort'] == 'DIST') {
                $entry['stem3'] = $entry['stem1'];
                $entry['stem1'] = null;

            } elseif ($entry['numeral_sort'] == 'ADVERB') {
                $entry['stem4'] = $entry['stem1'];
                $entry['stem1'] = null;
            }

        } elseif ($entry['part_of_speech'] == 'SUPINE') {
            if ($entry['which'] == 9) {
                if ($entry['variant'] == 9) {
                    $entry['stem4'] = $entry['stem1'];
                    $entry['stem1'] = null;
                }
            }
        }

        return $entry;
    }

    /**
     * Returns the entry possible inflections
     *
     * @param array $entry
     * @return array the inflections
     * @throws Exception
     */
    public function get_inflections($entry)
    {
        $part_of_speech = $entry['part_of_speech'];

        $attributes = $this->extract_attributes($part_of_speech, $entry);
        $key = "$part_of_speech|" . implode('|', $attributes);

        if (isset($this->inflections[$key])) {
            return $this->inflections[$key];
        }

        if (! isset($this->sql_selects[$part_of_speech])) {
            throw new Exception("Invalid inflection SQL select: $part_of_speech");
        }

        $sql = vsprintf($this->sql_selects[$part_of_speech], $attributes);

        $statement = $this->pdo->query($sql);
        $this->inflections[$key] = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->inflections[$key];
    }

    /**
     * Creates all possible inflections for the entry
     *
     * @param array $entry
     * @return array the inflected words
     * @throws Exception
     */
    public function inflect_entry($entry)
    {
        $entry = $this->fix_entry($entry);
        $inflections = $this->get_inflections($entry);
        $words = $this->add_endings($inflections, $entry);

        return $words;
    }

    /**
     * Inflects a dictionary entry, and inserts the inflected words in the table
     *
     * @param array $entry
     * @param string $part_of_speech used only for testing purposes
     * @return int the number of inflected words
     */
    public function inflect_insert_entries($entry, $part_of_speech = null)
    {
        if ($part_of_speech) {
            $entry['part_of_speech'] = $part_of_speech;
        }

        if ($this->part_of_speech and $entry['part_of_speech'] != $this->part_of_speech) {
            return 0;
        }

        $words = $this->inflect_entry($entry);
        $count = parent::insert_entries('word', $words);

        return $count;
    }

    /**
     * Inflects dictionary entries, and inserts inflected words in the table
     *
     * @param string $table_name unused
     * @param array $entries unused
     * @return int the number of inflected words
     */
    public function insert_entries($table_name = null, $entries = null)
    {
        $sql = "SELECT * from dictionary";

        $statement = $this->pdo->query($sql);
        $word_count = 0;

        while ($entry = $statement->fetch(PDO::FETCH_ASSOC)) {
            if ($entry['part_of_speech'] == 'PACK') { // TODO: remove when packons are handled
                continue;
            }

            $word_count += $this->inflect_insert_entries($entry);

            if ($entry['part_of_speech'] == 'V') {
                $word_count += $this->inflect_insert_entries($entry, 'SUPINE');
                $word_count += $this->inflect_insert_entries($entry, 'VPAR');
            }
        }

        return $word_count;
    }

    /**
     * Verifies the inflection is valid
     *
     * @param array $inflection
     * @param array $entry
     * @return boolean
     * @see LIST_SWEEP() in source/list_sweep.adb
     */
    public function is_valid_inflection($inflection, $entry)
    {
        if ($inflection['part_of_speech'] == 'V') {

            if ($inflection['which'] == 3 and
                $inflection['variant'] == 1 and
                $inflection['tense'] == 'PRES' and
                $inflection['voice'] == 'ACTIVE' and
                $inflection['mood'] == 'IMP' and
                $inflection['person'] == 2 and
                $inflection['number'] == 'S' and
                $inflection['ending_size'] == 0)
            {
                $stem = $this->get_stem($inflection['stem_key'], $entry, $inflection['id']);

                if (! preg_match('~(dic|duc|fac|fer)$~', $stem)) {
                    // this is not a verb built on dic/duc/fac/fer, eg "illud", rejects the shortened imperative
                    return false;
                }
            }

            if ($entry['verb_kind'] == 'IMPERS' and
                $inflection['person'] != 3)
            {
                // this is an impersonal verb at the first or second person, eg "contonas", rejects the inflection
                return false;
            }

            if ($entry['verb_kind'] == 'DEP' and
                $inflection['voice'] == 'ACTIVE' and
                in_array($inflection['mood'], ['IND', 'SUB', 'IMP', 'INF']))
            {
                // this is a deponent verb in the active voice, eg "adfat", rejects the inflection
                return false;
            }

            if ($entry['verb_kind'] == 'SEMIDEP') {

                if ($inflection['voice'] == 'PASSIVE' and
                    in_array($inflection['tense'], ['PRES', 'IMPF', 'FUT']) and
                    in_array($inflection['mood'], ['IND', 'SUB', 'IMP']))
                {
                    // this is a semi-deponent verb in the active voice, eg "auderis", rejects the inflection
                    return false;
                }

                if ($inflection['voice'] == 'ACTIVE' and
                    in_array($inflection['tense'], ['PERF', 'PLUP', 'FUTP']) and
                    in_array($inflection['mood'], ['IND', 'SUB', 'IMP']))
                {
                    // this is a semi-deponent verb in the active voice, eg "arfecisti", rejects the inflection
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Creates and loads the inflected words in the database
     *
     * @param string $part_of_speech used only for testing purposes
     * @return int the number of inflected words
     */
    public function load_words($part_of_speech = null)
    {
        $this->part_of_speech = $part_of_speech;
        $count = $this->load_table('word');

        return $count;
    }

    /**
     * Returns an entry inflected words for testing purposes
     *
     * @param int $entry_id
     * @return array the inflected words
     */
    public function test_inflect_entry($entry_id)
    {
        $sql = "SELECT * from dictionary WHERE id = $entry_id";
        $statement = $this->pdo->query($sql);

        if (! $entry = $statement->fetch(PDO::FETCH_ASSOC)) {
            return null;
        }

        $words = $this->inflect_entry($entry);

        return $words;
    }
}
