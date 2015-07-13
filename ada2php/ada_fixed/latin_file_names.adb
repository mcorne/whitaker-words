package body LATIN_FILE_NAMES is
    --  In order to port the program LATIN to another system, the file names
    --  must be made consistent with that system.
    --  This package is withed into all units that declare external file names
    --  and its modification should take care of the system dependence of names
    --  Then one needs to copy the ASCII data files on the disk to files named
    --  in accordance with the modified package.
    --  Note that there are some files that take extensions in DOS, and there
    --  is a function that takes those extensions and makes a legal file name.
    --  In other systems this will have to be handled to create a legal file name

    --  This package can be presented as the first to be compiled, however
    --  the actual need for file mames does not come until deep in the system
    --  Conventionally, the naming is put off until the file is actually
    --  used, and the name is passed as a parameter from there to the
    --  earlier procedures which call them

    --  The following files are used in the DOS LATIN program and are
    --  DOS legal, names no longer than 8 characters, with '.' and extension


    --  Single files, that is, that need only the one FULL name, no variations
    --  These files are input files and may have any name legal in your system
    --  and contain the ASCII information copied from the porting system

    INFLECTIONS_FULL_NAME     : constant STRING := "INFLECTS.LAT";
    INFLECTIONS_SECTIONS_NAME : constant STRING := "INFLECTS.SEC";

    UNIQUES_FULL_NAME      : constant STRING := "UNIQUES.LAT";
    ADDONS_FULL_NAME       : constant STRING := "ADDONS.LAT";

    --  These files may be created and used by the program
    MODE_FULL_NAME         : constant STRING := "WORD.MOD";
    OUTPUT_FULL_NAME       : constant STRING := "WORD.OUT";
    UNKNOWNS_FULL_NAME     : constant STRING := "WORD.UNK";
    PARSE_FULL_NAME        : constant STRING := "WORD.PRS";

    --  These file names are used with extensions (e.g., GEN, SPE, LOC)
    --  for the various dictionaries
    --  The function ADD_FILE_NAME_EXTENSION below is used to create
    --  a full file name
    --  Note that for DOS they are not complete names (no '.')
    --  but DOS is forgiving and will give it a pass

    DICTIONARY_FILE_NAME  : constant STRING := "DICT";
    DICT_FILE_NAME        : constant STRING := "DICTFILE";
    DICT_LINE_NAME        : constant STRING := "DICTLINE";
    STEM_LIST_NAME        : constant STRING := "STEMLIST";
    STEM_FILE_NAME        : constant STRING := "STEMFILE";
    INDX_FILE_NAME        : constant STRING := "INDXFILE";

    function ADD_FILE_NAME_EXTENSION(NAME, EXTENSION : STRING) return STRING is
    begin
        --  This is the version that creates a DOS file name
        --  One that has a name, a '.', and an extension no longer than 3 characters
        --  Arbitarily, we also truncate the NAME to 8 characters
        --  To port to another system, one needs to do this function appropriately
//*
      NAME_LENGTH : INTEGER := NAME'LENGTH;
      EXTENSION_LENGTH : INTEGER := EXTENSION'LENGTH;
      if NAME_LENGTH >= 8  then
        NAME_LENGTH := 8;
      end if;
      if EXTENSION'LENGTH >= 3  then
        EXTENSION_LENGTH := 3;
      end if;
      return NAME(1..NAME_LENGTH) & '.' & EXTENSION(1..EXTENSION_LENGTH);
*//
        $FILENAME = substr($NAME, 0, 8) . '.' . substr($EXTENSION, 0, 3);

        return $FILENAME;
    end ADD_FILE_NAME_EXTENSION;
end LATIN_FILE_NAMES;