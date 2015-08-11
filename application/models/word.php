<?php
require_once 'common.php';

class word extends common
{
    public $endings;

    public $table_word = [
        'table' => '
            CREATE TABLE word (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            entry_id      INTEGER,
            inflection_id INTEGER,
            word          TEXT)
        ',
        'index' => 'CREATE INDEX "words" ON word (word)',
    ];

    /**
     *
     * @param string $comparison
     * @return array
     */
    public function get_adverb_endings($comparison)
    {
        $key = "ADV|$comparison";

        $sql = "SELECT
                    id,
                    stem_key
                FROM inflection
                WHERE part_of_speech = 'ADV'
                AND comparison = '$comparison'";

        $endings = $this->get_endings($key, $sql);

        return $endings;
    }

    /**
     *
     * @param string $key
     * @param string $sql
     * @return array
     */
    public function get_endings($key, $sql)
    {
        if (! isset($this->endings[$key])) {
            $statement = $this->pdo->query($sql);
            $this->endings[$key] = $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->endings[$key];
    }

    /**
     *
     * @param int $which
     * @param int $variant
     * @param string $gender
     * @return array
     */
    public function get_noun_endings($which, $variant, $gender)
    {
        $key = "N|$which|$variant|$gender";

        $sql = "SELECT
                    id,
                    ending,
                    stem_key
                FROM inflection
                WHERE part_of_speech = 'N'
                AND which = $which
                AND (variant = 0 OR variant = $variant)
                AND (gender = '$gender' OR gender = 'C' OR gender = 'X')";

        $endings = $this->get_endings($key, $sql);

        return $endings;
    }

    /**
     *
     * @param array $entry
     * @return array
     * @throws Exception
     */
    public function inflect_entry($entry)
    {
        switch ($entry['part_of_speech']) {
            case 'ADV':
                $words = $this->inflect_adverb($entry['id'], $entry['stem1'], $entry['stem2'], $entry['stem3'], $entry['comparison']);
                break;

            case 'N':
                $words = $this->inflect_noun($entry['id'], $entry['stem1'], $entry['stem2'], $entry['which'], $entry['variant'], $entry['gender']);
                break;

            case 'ADJ':
            case 'CONJ':
            case 'INTERJ':
            case 'NUM':
            case 'PACK': // see pronoun
            case 'PREP':
            case 'PRON':
            case 'V': // including SUPINE, VPAR
                $words = null;
                break;

            default:
                throw new Exception("Invalid part of speech: {$entry['part_of_speech']} in entry id: {$entry['id']}");
        }

        return $words;
    }

    /**
     *
     * @param int $entry_id
     * @param string $stem1
     * @param string $stem2
     * @param string $stem3
     * @param string $comparison
     * @return array
     * @throws Exception
     */
    public function inflect_adverb($entry_id, $stem1, $stem2, $stem3, $comparison)
    {
        $endings = $this->get_adverb_endings($comparison);
        $inflections = [];

        foreach ($endings as $ending) {
            if ($ending['stem_key'] == 1) {
                $stem = $stem1;
            } elseif ($ending['stem_key'] == 2) {
                $stem = $stem2;
            } elseif ($ending['stem_key'] == 3) {
                $stem = $stem3;
            } else {
                throw new Exception("Invalid stem key: {$ending['stem_key']} in inflection id: {$ending['id']}");
            }

            if (! $stem) {
                throw new Exception("Empty stem in entry id: $entry_id");
            }

            if ($stem == 'zzz') {
                continue;
            }

            $inflections[] = [
                'entry_id'      => $entry_id,
                'inflection_id' => $ending['id'],
                'word'          => $stem,
            ];
        }

        return $inflections;
    }

    /**
     *
     * @param int $entry_id
     * @param string $stem1
     * @param string $stem2
     * @param int $which
     * @param int $variant
     * @param string $gender
     * @return array
     * @throws Exception
     */
    public function inflect_noun($entry_id, $stem1, $stem2, $which, $variant, $gender)
    {
        $endings = $this->get_noun_endings($which, $variant, $gender);
        $inflections = [];

        foreach ($endings as $ending) {
            if ($ending['stem_key'] == 1) {
                $stem = $stem1;
            } elseif ($ending['stem_key'] == 2) {
                $stem = $stem2;
            } else {
                throw new Exception("Invalid stem key: {$ending['stem_key']} in inflection id: {$ending['id']}");
            }

            if (! $stem) {
                throw new Exception("Empty stem in entry id: $entry_id");
            }

            if ($stem == 'zzz') {
                continue;
            }

            $inflections[] = [
                'entry_id'      => $entry_id,
                'inflection_id' => $ending['id'],
                'word'          => $stem . $ending['ending'],
            ];
        }

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
            if ($words = $this->inflect_entry($entry)) {
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