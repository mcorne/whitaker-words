<?php
class ada2php
{
    function can_update_php_file($basename, $force)
    {
        $filename = dirname(__DIR__) . "/php/$basename.php";

        if ($exists = file_exists($filename) and ! $force) {
            $message = "File exists $filename\n"
                     . "Use -f to force the update";
             throw new Exception($message);
        }

        return [$filename, $exists];
    }

    function convert_ada_to_php($fixes, $content)
    {
        $patterns     = array_keys($fixes);
        $replacements = array_values($fixes);

        $fixed = preg_replace($patterns, $replacements, $content);

        return $fixed;
    }

    function convert_names_to_lower($content)
    {
        $patterns = [
            '\$\w+',            // eg $ABC
            'function [\w]+\(', // eg function FOO(
            '\w+::\w+\(',       // eg FOO::BAR(
            'class \w+',        // eg class BAZ
            '/\* \w+ \*/',      // /* STRING */
            '// \w+\n',         // // STRING
            '\w+.php',          // TEXT_IO.php
        ];

        $pattern = '~(' . implode('|', $patterns) . ')~';

        $replacement = function ($matches) {
            return strtolower($matches[0]);
        };

        $content = preg_replace_callback($pattern, $replacement, $content);

        return $content;
    }

    function help()
    {
        $message = "Usage: ada2php <package name> [-f]\n"
                 . "-f    Forces the update\n"
                 . "Example: ada2php strings_pacakge -f";
        throw new Exception($message);
    }

    function read_ada_file($basename)
    {
        $filename = dirname(__DIR__) . "/ada_fixed/$basename.adb";

        if (! file_exists($filename) or ! $content = file_get_contents($filename)) {
            throw new Exception("Cannot read $filename");
        }

        return $content;
    }

    function read_ada2php_custom_fixes($basename)
    {
        $filename = dirname(__DIR__) . "/ada2php_fixes/$basename.php";
        $fixes = file_exists($filename) ? include($filename) : [];

        return $fixes;
    }

    function read_ada2php_fixes()
    {
        $filename = __DIR__ . '/ada2php_fixes.php';

        if (! file_exists($filename)) {
            throw new Exception("Cannot read $filename");
        }

        $fixes = include($filename);

        return $fixes;
    }

    function run($basename, $force)
    {
        list($php_filename, $php_file_exists) = $this->can_update_php_file($basename, $force);

        $fixes  = $this->read_ada2php_fixes();
        $fixes += $this->read_ada2php_custom_fixes($basename);

        $ada_content = $this->read_ada_file($basename);
        $php_content = "<?php\n" . $this->convert_ada_to_php($fixes, $ada_content);
        $php_content = $this->convert_names_to_lower($php_content);

        $this->write_php_file($php_filename, $php_content);

        $message = $php_file_exists ? "File updated $php_filename" : "File created $php_filename";

        return $message;
    }

    function write_php_file($php_filename, $php_content)
    {
        if (! file_put_contents($php_filename, $php_content)) {
            throw new Exception("Cannot write $php_filename");
        }
    }
}

try {
    $basename = isset($argv[1]) ? $argv[1] : null;
    $force = ! empty($argv[2]);

    $ada2php = new ada2php();
    $message = $ada2php->run($basename, $force);

} catch (Exception $e) {
    $message = $e->getTraceAsString();
}

echo $message;

