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

    public function get_noun_endings($which, $variant, $gender)
    {
        $key = "N|$which|$variant|$gender";

        if (! isset($this->endings[$key])) {
            $sql = "SELECT
                        id,
                        ending,
                        stem_key
                    FROM inflection
                    WHERE part_of_speech = 'N'
                    AND which = $which
                    AND (variant = 0 OR variant = $variant)
                    AND (gender = '$gender' OR gender = 'C' OR gender = 'X')";

            $statement = $this->pdo->query($sql);
            $this->endings[$key] = $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->endings[$key];
    }

    public function inflect_noun($entry_id, $stem1, $stem2, $which, $variant, $gender)
    {
        $endings = $this->get_noun_endings($which, $variant, $gender);
        $noun_inflections = [];

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

            $noun_inflections[] = [
                'entry_id'      => $entry_id,
                'inflection_id' => $ending['id'],
                'word'          => $stem . $ending['ending'],
            ];
        }

        return $noun_inflections;
    }

    public function insert_entries($table_name = null, $entries = null)
    {
        $sql = "SELECT * from dictionary";
        // $sql = "SELECT * from dictionary WHERE id = 33805"; TODO: remove
        $statement = $this->pdo->query($sql);
        $word_count = 0;

        while ($entry = $statement->fetch(PDO::FETCH_ASSOC)) {
            switch ($entry['part_of_speech']) {
                case 'N':
                    $words = $this->inflect_noun($entry['id'], $entry['stem1'], $entry['stem2'], $entry['which'], $entry['variant'], $entry['gender']);
                    break;

                case 'ADJ':
                case 'ADV':
                case 'CONJ':
                case 'INTERJ':
                case 'NUM':
                case 'PACK': // see pronoun
                case 'PREP':
                case 'PRON':
                case 'V': // including SUPINE, VPAR
                    break;

                default:
                    throw new Exception("Invalid part of speech: {$entry['part_of_speech']} in entry id: {$entry['id']}");
            }

            $word_count += parent::insert_entries('word', $words);

            if ($word_count == 10000) {
                break; // TODO: remove
            }
        }

        return $word_count;
    }

    public function load_words()
    {
        $count = $this->load_table('word', $this->table_word);

        return $count;
    }
}