<?php
require_once 'common.php';

class word extends common
{
    public $endings;

    /**
     *
     * @var array
     * @see self::$inflection_select, the vsprintf() args order must be the same in both arrays
     */
    public $inflection_attributes = [
        'ADJ'    => ['which', 'variant', 'comparison'],
        'ADV'    => ['comparison'],
        'CONJ'   => [],
        'INTERJ' => [],
        'N'      => ['which', 'variant', 'gender'],
        'NUM'    => ['which', 'variant', 'numeral_sort'],
        'PACK'   => [],
        'PREP'   => ['cases'],
        'PRON'   => ['which', 'variant'],
        'V'      => [],
    ];

    /**
     * @var array
     * @see self::$inflection_attributes, the vsprintf() args order must be the same in both arrays
     * @see leveraging inflection::$sql_indexes
     */
    public $inflection_select = [
        'ADJ'    => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "ADJ"
            AND (which = %1$d OR which = 0)
            AND (variant = %2$d OR variant = 0)
            AND (comparison = "%3$s" OR "%3$s" = "X")',

        'ADV'    => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "ADV"
            AND comparison = "%s"',

        'CONJ'   => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "CONJ"',

        'INTERJ' => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "INTERJ"',

        'N'      => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "N"
            AND which = %1$d
            AND (variant = %2$d OR variant = 0)
            AND (gender = "%3$s" OR gender = "C" OR gender = "X")',
        'NUM'    => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "NUM"
            AND (which = %1$d OR which = 0)
            AND (variant = %2$d OR variant = 0)
            AND (numeral_sort = "%3$s" OR "%3$s" = "X")',

        'PACK'   => '',

        'PREP'   => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "PREP"
            AND cases = "%s"',

        'PRON'   => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "PRON"
            AND which = %1$d
            AND (variant = %2$d OR variant = 0)',

        'V'      => '',
    ];

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
     *
     * @var string
     */
    public $sql_views_and_indexes = '
        DROP INDEX IF EXISTS word_word;
        CREATE INDEX word_word ON word (word);

        DROP VIEW IF EXISTS words_by_part_of_speech;
        CREATE VIEW words_by_part_of_speech AS
        SELECT
            dictionary.part_of_speech,
            count(dictionary.part_of_speech) AS count
        FROM word
        JOIN dictionary ON dictionary.id = word.entry_id
        GROUP BY part_of_speech
        UNION
        SELECT
            "-- Total --" AS part_of_speech,
            count(*) AS count
        FROM word;
    ';

    public function __construct()
    {
        parent::__construct();

        $this->inflection_attributes = array_map('array_flip', $this->inflection_attributes);
    }

    public function add_endings($endings, $entry)
    {
        $inflections = [];

        foreach ($endings as $ending) {
            switch ($ending['stem_key']) {
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
                    throw new Exception("Invalid stem key: {$ending['stem_key']} in inflection id: {$ending['id']}");
            }

            if (! $stem) {
                throw new Exception("Empty stem or invalid stem key: {$ending['stem_key']} in entry id: {$entry['id']}");
            }

            if ($stem == 'zzz') {
                continue;
            }

            $inflections[] = [
                'entry_id'      => $entry['id'],
                'inflection_id' => $ending['id'],
                'word'          => $stem . $ending['ending'],
            ];
        }

        return $inflections;
    }

    public function extract_attributes($part_of_speech, $entry)
    {
        if (! isset($this->inflection_attributes[$part_of_speech])) {
            throw new Exception("Unavailable inflection attributes for: $part_of_speech");
        }

        $attributes = array_intersect_key($entry, $this->inflection_attributes[$part_of_speech]);

        return $attributes;
    }

    public function fix_entry($entry)
    {
        if ($entry['part_of_speech'] == 'ADJ' and $entry['which'] == 0 and $entry['variant'] == 0) {
            if ($entry['comparison'] == 'COMP') {
                $entry['stem3'] = $entry['stem1'];
                $entry['stem1'] = null;

            } elseif ($entry['comparison'] == 'SUPER') {
                $entry['stem4'] = $entry['stem1'];
                $entry['stem1'] = null;
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
        }

        return $entry;
    }

    public function get_endings($entry)
    {
        $part_of_speech = $entry['part_of_speech'];

        $attributes = $this->extract_attributes($part_of_speech, $entry);
        $key = "$part_of_speech|" . implode('|', $attributes);

        if (isset($this->endings[$key])) {
            return $this->endings[$key];
        }

        if (! isset($this->inflection_select[$part_of_speech])) {
            throw new Exception("Invalid inflection SQL select: $part_of_speech");
        }

        if (empty($this->inflection_select[$part_of_speech])) {
            // not processed yet, temporary, TODO: remove
            return null;
        }

        $sql = vsprintf($this->inflection_select[$part_of_speech], $attributes);

        $statement = $this->pdo->query($sql);
        $this->endings[$key] = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->endings[$key];
    }

    /**
     *
     * @param array $entry
     * @return array
     * @throws Exception
     */
    public function inflect_entry($entry)
    {
        $entry = $this->fix_entry($entry);

        if (! $endings = $this->get_endings($entry)) { // TODO: remove test when all part of speech processed
            return null;
        }

        $inflections = $this->add_endings($endings, $entry);

        return $inflections;
    }

    /**
     *
     * @param string $table_name unused
     * @param array $entries unused
     * @return type
     */
    public function insert_entries($table_name = null, $entries = null)
    {
        $sql = "SELECT * from dictionary";
        $statement = $this->pdo->query($sql);
        $word_count = 0;

        while ($entry = $statement->fetch(PDO::FETCH_ASSOC)) {
            if ($words = $this->inflect_entry($entry)) { // TODO: remove test when all part of speech processed
                $word_count += parent::insert_entries('word', $words);
            }
        }

        return $word_count;
    }

    /**
     *
     * @return int
     */
    public function load_words()
    {
        $count = $this->load_table('word');

        return $count;
    }

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