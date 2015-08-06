<?php
set_include_path(__DIR__ . '/../models');
require_once 'inflection.php';
$inflection = new inflection();

$lines = [];
// $lines[] = "ADJ 1 1 NOM S M POS 1 2 us X A";
// $lines[] = "ADV POS 1 0 X A";
// $lines[] = "CONJ 1 0 X A";
// $lines[] = "INTERJ 1 0 X A";
// $lines[] = "N 1 1 NOM S C  1 1 a X A";
// $lines[] = "NUM 1 1 NOM S M CARD 1 2 us X A";
// $lines[] = "VPAR 1 0 NOM S X PRES ACTIVE PPL 1 3 ans X A";
// $lines[] = "PREP GEN 1 0 X A";
// $lines[] = "PRON 1 0 GEN S X 2 3 jus X A";
// $lines[] = "SUPINE 0 0 ACC S N 4 2 um X A";
// $lines[] = "V 1 1 PRES ACTIVE IND  2 S  2 2 as X A";

$inflections = $inflection->load_inflections($lines);
// print_r($inflections);
echo count($inflections);
