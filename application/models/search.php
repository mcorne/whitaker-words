<?php
require_once 'common.php';

/**
 * Latin word search
 */
class search extends common
{
    /**
     * The select statement to search an inflected word
     *
     * @var string
     */
    public $sql_select = '
        SELECT
            dictionary.id             AS entry_id,
            dictionary.stem1,
            dictionary.stem2,
            dictionary.stem3,
            dictionary.stem4,
            dictionary.part_of_speech AS entry_part_of_speech,
            dictionary.which          AS entry_which,
            dictionary.variant        AS entry_variant,
            dictionary.comparison     AS entry_comparison,
            dictionary.gender         AS entry_gender,
            dictionary.noun_kind      AS entry_noun_kind,
            dictionary.numeral_sort   AS entry_numeral_sort,
            dictionary.numeral_value  AS entry_numeral_value,
            dictionary.pronoun_kind   AS entry_pronoun_kind,
            dictionary.cases          AS entry_cases,
            dictionary.verb_kind      AS entry_verb_kind,
            dictionary.age            AS entry_age,
            dictionary.area           AS entry_area,
            dictionary.geography      AS entry_geography,
            dictionary.frequency      AS entry_frequency,
            dictionary.source         AS entry_source,
            dictionary.meaning        AS entry_meaning,
            dictionary.line_number    AS entry_line_number,
            inflection.id             AS inflection_id,
            inflection.part_of_speech AS inflection_part_of_speech,
            inflection.which          AS inflection_which,
            inflection.variant        AS inflection_variant,
            inflection.cases          AS inflection_cases,
            inflection.number         AS inflection_number,
            inflection.gender         AS inflection_gender,
            inflection.comparison     AS inflection_comparison,
            inflection.numeral_sort   AS inflection_numeral_sort,
            inflection.tense          AS inflection_tense,
            inflection.voice          AS inflection_voice,
            inflection.mood           AS inflection_mood,
            inflection.person         AS inflection_person,
            inflection.stem_key,
            inflection.ending_size,
            inflection.ending,
            inflection.age            AS inflection_age,
            inflection.frequency      AS inflection_frequency,
            inflection.line_number    AS inflection_line_number
        FROM word
        JOIN inflection ON inflection.id = word.inflection_id
        JOIN dictionary ON dictionary.id = word.entry_id
        WHERE word.word = %s;
    ';


    /**
     * The inflected words views
     *
     * @var string
     */
    public $sql_views_and_indexes = '
        DROP VIEW IF EXISTS search_word;
        CREATE VIEW search_word AS
        SELECT
            word.word,
            inflection.part_of_speech,
            dictionary.which,
            dictionary.variant,
            inflection.cases,
            inflection.tense,
            inflection.voice,
            inflection.mood,
            inflection.person,
            inflection.number,
            CASE WHEN dictionary.gender IS NOT NULL AND dictionary.gender != "X" THEN dictionary.gender ELSE inflection.gender END AS gender,
            CASE WHEN dictionary.comparison IS NOT NULL AND dictionary.comparison != "X" THEN dictionary.comparison ELSE inflection.comparison END AS comparison,
            dictionary.meaning,
            word.id,
            dictionary.id AS dictionary_id,
            inflection.id AS inflection_id
        FROM word
        JOIN inflection ON inflection.id = word.inflection_id
        JOIN dictionary ON dictionary.id = word.entry_id
        ORDER BY
            inflection.part_of_speech,
            dictionary.which,
            dictionary.variant,
            inflection.tense,
            inflection.voice,
            inflection.mood,
            inflection.person,
            inflection.number DESC,
            gender;
    ';

    /**
     * Creates the wiews
     */
    public function load_search()
    {
        $this->pdo->exec($this->sql_views_and_indexes);
    }

    /**
     * Verifies if the inflected word is actually valid
     *
     * @param array $inflection
     * @return boolean
     * @see LIST_SWEEP() in source/list_sweep.adb
     * @todo move to word class
     */
    public function is_valid_inflection($inflection)
    {
        if ($inflection['inflection_part_of_speech'] == 'V') {
            if ($inflection['inflection_which'] == 3 and
                $inflection['inflection_variant'] == 1 and
                $inflection['inflection_tense'] == 'PRES' and
                $inflection['inflection_voice'] == 'ACTIVE' and
                $inflection['inflection_mood'] == 'IMP' and
                $inflection['inflection_person'] == 2 and
                $inflection['inflection_number'] == 'S' and
                $inflection['ending_size'] == 0)
            {
                $stem = $this->get_stem($inflection['stem_key'], $inflection, $inflection['inflection_id']);

                if (! preg_match('~(dic|duc|fac|fer)$~', $stem)) {
                    // this is not a verb built on dic/duc/fac/fer, eg "illud", rejects the shortened imperative
                    return false;
                }
            }

            if ($inflection['entry_verb_kind'] == 'IMPERS' and
                $inflection['inflection_person'] != 3)
            {
                // this is an impersonal verb at the first or second person, eg "contonas", rejects the inflection
                return false;
            }

            if ($inflection['entry_verb_kind'] == 'DEP' and
                $inflection['inflection_voice'] == 'ACTIVE' and
                in_array($inflection['inflection_mood'], ['IND', 'SUB', 'IMP', 'INF']))
            {
                // this is a deponent verb in the active voice, eg "adfat", rejects the inflection
                return false;
            }

            if ($inflection['entry_verb_kind'] == 'SEMIDEP' and // TODO: fix
                $inflection['inflection_voice'] == 'ACTIVE' and
                in_array($inflection['inflection_mood'], ['IND', 'SUB', 'IMP', 'INF']))
            {
                // this is a deponent verb in the active voice, eg "adfat", rejects the inflection
                return false;
            }
        }

        return true;
    }

    /**
     * Removes invalid inflections
     *
     * @param array $inflections
     * @return array the valid inflections
     * @todo move to word class
     */
    public function remove_invalid_inflections($inflections)
    {
        $valid_inflections = [];

        foreach ($inflections as $inflection) {
            if ($this->is_valid_inflection($inflection)) {
                $valid_inflections[] = $inflection;
            }
        }

        return $valid_inflections;
    }

    /**
     * Searches an inflected word
     *
     * @param string $word
     * @return array the corresponding inflections and dictionary entries
     */
    public function search_word($word)
    {
        $word = $this->pdo->quote($word);
        $sql = sprintf($this->sql_select, $word);
        $statement = $this->pdo->query($sql);
        $inflections = $statement->fetchAll(PDO::FETCH_ASSOC);
        $inflections = $this->remove_invalid_inflections($inflections);

        return $inflections;
    }
}
