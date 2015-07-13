<?php
class latin_file_names
{
    // In order to port the program LATIN to another system, the file names
    // must be made consistent with that system.
    // This package is withed into all units that declare external file names
    // and its modification should take care of the system dependence of names
    // Then one needs to copy the ASCII data files on the disk to files named
    // in accordance with the modified package.
    // Note that there are some files that take extensions in DOS, and there
    // is a function that takes those extensions and makes a legal file name.
    // In other systems this will have to be handled to create a legal file name

    // This package can be presented as the first to be compiled, however
    // the actual need for file mames does not come until deep in the system
    // Conventionally, the naming is put off until the file is actually
    // used, and the name is passed as a parameter from there to the
    // earlier procedures which call them

    // The following files are used in the DOS LATIN program and are
    // DOS legal, names no longer than 8 characters, with '.' and extension

    // Single files, that is, that need only the one FULL name, no variations
    // These files are input files and may have any name legal in your system
    // and contain the ASCII information copied from the porting system

    const INFLECTIONS_FULL_NAME = "INFLECTS.LAT";
    const INFLECTIONS_SECTIONS_NAME = "INFLECTS.SEC";

    const UNIQUES_FULL_NAME = "UNIQUES.LAT";
    const ADDONS_FULL_NAME = "ADDONS.LAT";

    // These files may be created and used by the program
    const MODE_FULL_NAME = "WORD.MOD";
    const OUTPUT_FULL_NAME = "WORD.OUT";
    const UNKNOWNS_FULL_NAME = "WORD.UNK";
    const PARSE_FULL_NAME = "WORD.PRS";

    // These file names are used with extensions (e.g., GEN, SPE, LOC)
    // for the various dictionaries
    // The function ADD_FILE_NAME_EXTENSION below is used to create
    // a full file name
    // Note that for DOS they are not complete names (no '.')
    // but DOS is forgiving and will give it a pass

    const DICTIONARY_FILE_NAME = "DICT";
    const DICT_FILE_NAME = "DICTFILE";
    const DICT_LINE_NAME = "DICTLINE";
    const STEM_LIST_NAME = "STEMLIST";
    const STEM_FILE_NAME = "STEMFILE";
    const INDX_FILE_NAME = "INDXFILE";

    public static function add_file_name_extension($name, $extension /* string */) // string
    {
        // This is the version that creates a DOS file name
        // One that has a name, a '.', and an extension no longer than 3 characters
        // Arbitarily, we also truncate the NAME to 8 characters
        // To port to another system, one needs to do this function appropriately
        $filename = substr($name, 0, 8) . '.' . substr($extension, 0, 3);

        return $filename;
    }
}