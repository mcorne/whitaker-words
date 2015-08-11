<?php
require_once 'common.php';

class word extends common
{
    public $endings;

    public $inflection_attributes = [
        'ADJ'    => [],
        'ADV'    => ['comparison'],
        'CONJ'   => [],
        'INTERJ' => [],
        'N'      => ['which', 'variant', 'gender'],
        'NUM'    => [],
        'PACK'   => [],
        'PREP'   => ['cases'],
        'PRON'   => [],
        'V'      => [],
    ];

    public $inflection_select = [
        'ADJ'    => '',

        'ADV'    => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "ADV"
            AND comparison = "%s"',

        'CONJ'   => '',

        'INTERJ' => '',

        'N'      => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "N"
            AND which = %1$d
            AND (variant = %2$d OR variant = 0)
            AND (gender = "%3$s" OR gender = "C" OR gender = "X")',
        'NUM'    => '',

        'PACK'   => '',

        'PREP'   => '
            SELECT id, ending, stem_key FROM inflection
            WHERE part_of_speech = "PREP"
            AND cases = "%s"',

        'PRON'   => '',

        'V'      => '',
    ];

    public $table_word = [
        'table' => '
            CREATE TABLE word (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            entry_id      INTEGER,
            inflection_id INTEGER,
            word          TEXT)',

        'index' => 'CREATE INDEX "words" ON word (word)',
    ];

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
        $count = $this->load_table('word', $this->table_word);

        return $count;
    }
}