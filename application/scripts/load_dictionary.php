<?php
set_include_path(__DIR__ . '/../models');
require_once 'dictionary.php';
$dictionary = new dictionary();

$lines = [];
// $lines[] = "abact              abact                                                    ADJ    1 1 POS          X X X E S driven away/off/back; forced to resign (office); restrained by; passed (night);";
// $lines[] = "abject             abject             abjecti            abjectissi         ADJ    1 1 X            X X X B L downcast, dejected;";
// $lines[] = "abdicative                                                                  ADV    POS              D X X E S negatively;";
// $lines[] = "abjecte            abjectius          abjectissime                          ADV    X                X X X C L in spiritless manner;";
// $lines[] = "ac                                                                          CONJ                    X X X A O and, and also, and besides;";
// $lines[] = 'aelinon                                                                     INTERJ                  X X X F O exclamation of sorrow; "alas for Linus";';
// $lines[] = "abac               abac                                                     N      2 1 M T          E E X C E small table for cruets, credence, shelf/niche near altar for Eucharist; buffet;";
// $lines[] = "Act                                                                         N      9 8 N T          E E X D E Acts (abbreviation);";
// $lines[] = "amb                                                                         NUM    1 2 CARD       0 X X X B O both; two of pair; two considered together, both parties; each of two;";
// $lines[] = "biscentum          biscentesim        biscenten          biscent            NUM    2 0 X        200 X X X C E two hundred;";
// $lines[] = "qu                 cu                                                       PACK   1 0 REL          X X X A X (w/-cumque) who/whatever, no matter who/what, in any time/way, however small;";
// $lines[] = "ab                                                                          PREP   ABL              X X X A O by (agent), from (departure, cause, remote origin/time); after (reference);";
// $lines[] = "aliqu              alicu                                                    PRON   1 0 INDEF        X X X A O anyone/anybody/anything; someone; some/few; some (particular) thing;";
// $lines[] = "abaestu            abaestu            abaestuav          abaestuat          V      1 1 INTRANS      D X X F S wave down; hang down richly (poet.);";
// $lines[] = "cumi                                                                        V      9 9 X            E E Q F E arise;";

$entries = $dictionary->load_dictionary($lines);
// print_r($entries);
echo count($entries);
