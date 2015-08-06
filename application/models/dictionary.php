<?php
require_once 'file.php';

class dictionary
{
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

    public $geo_type = [
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

    public $noun_type = [
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

    public $numeral_value_type;

    public $pronoun_type = [
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

    public $verb_type = [
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
    }
}