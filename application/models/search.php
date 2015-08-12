<?php
require_once 'common.php';

class search extends common
{
    public $sql_select = '
        SELECT
            dictionary.id             AS entry_id,
            dictionary.stem1          AS entry_stem1,
            dictionary.stem2          AS entry_stem2,
            dictionary.stem3          AS entry_stem3,
            dictionary.stem4          AS entry_stem4,
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
            inflection.stem_key       AS inflection_stem_key,
            inflection.ending_size    AS inflection_ending_size,
            inflection.ending         AS inflection_ending,
            inflection.age            AS inflection_age,
            inflection.frequency      AS inflection_frequency,
            inflection.line_number    AS inflection_line_number
        FROM word
        JOIN inflection ON inflection.id = word.inflection_id
        JOIN dictionary ON dictionary.id = word.entry_id
        WHERE word.word = %s;
    ';

    /**
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

    public function load_search()
    {
        $this->pdo->exec($this->sql_views_and_indexes);
    }

    public function search_word($word)
    {
        $word = $this->pdo->quote($word);
        $sql = sprintf($this->sql_select, $word);
        $statement = $this->pdo->query($sql);
        $inflection = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $inflection;
    }

}