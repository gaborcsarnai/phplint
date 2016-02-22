<?php
/**
 * The PHPLint class.
 *
 * PHPLint is a PHP code quality tool. It is inspired by Douglas Crockford's
 * JSLint.
 *
 * @author Gabor Csarnai <gabor@antavo.com>
 */
class PHPLint {
    /**
     * Rules to check in source file. Each of them consists of a description at
     * index 0, and a regexp pattern at index 1. The lenght of the first
     * capture of the regexp will be used when reporting error position. (It is
     * advised to anchor the first capture to the beginning of the string.)
     *
     * @var array
     */
    protected $rules = [
        [
            'Put a space after each control structure keyword',
            '/(.*(if|else|elseif|switch|for|foreach|while))\(/'
        ],
        [
            'Unexpected space after \'(\'',
            '/(.*\()\s[^\n]/'
        ],
        [
            'Unexpected space before \')\'',
            '/(.*[^\s]+)\s\)/'
        ],
        [
            '25 characters should be long enough for a variable name',
            '/(.*)\$[a-z_]\w{25,}\b/i'
        ],
        [
            'Use alternative syntax for control structures when mixing outputs',
            '/(.*)\{\s*\?\>/'
        ],
        [
            'Use alternative syntax for control structures when mixing outputs',
            '/(.*\<\?php\s+)\}/'
        ],
    ];

    /**
     * @param string $filename  Filename to process.
     * @return array  Returns an array of arrays with the following elements:
     * (0) rule description, (1) line number, (2) position.
     */
    public function __invoke($filename) {
        return $this->process($filename);
    }

    /**
     * @param string $description  Rule description.
     * @param string $pattern  Regex pattern with delimiters and optional
     * modifiers.
     * @return $this  Returns the instance object for method chaining.
     */
    public function addRule($description, $pattern) {
        $this->rules[] = [$description, $pattern];
        return $this;
    }

    /**
     * @param array $rules
     * @return $this  Returns the instance object for method chaining.
     */
    public function addRules(array $rules) {
        foreach ($rules as $pattern => $description) {
            $this->addRule($description, $pattern);
        }
        return $this;
    }

    /**
     * Matches line against pattern then returns column position of first
     * occurrence.
     *
     * @param string $line  Line to match against pattern.
     * @param string $pattern  Regex pattern with delimiters and optional
     * modifiers.
     * @return int  Number of column where first pattern occurrence found. 0
     * means that none found.
     * @static
     */
    public static function match($line, $pattern) {
        if (preg_match($pattern, $line, $matches)) {
            return isset($matches[1])
                ? strlen($matches[1]) + 1
                : 1;
        }
        return 0;
    }

    /**
     * @param string $line  Line to check.
     * @param int $number  Line number to include in error report.
     * @return array  Returns an array of arrays with the following elements:
     * (0) rule description, (1) line number, (2) position.
     */
    public function checkLine($line, $number = 1) {
        $errors = [];
        foreach ($this->rules as $rule) {
            if ($position = $this->match($line, $rule[1])) {
                $errors[] = [$rule[0], $number, $position];
            }
        }
        return $errors;
    }

    /**
     * Checks file against quality criteria.
     *
     * @param string $filename  Filename to process.
     * @return array  Returns an array of arrays with the following elements:
     * (0) rule description, (1) line number, (2) position.
     * @throws RuntimeException  On file open error.
     */
    public function process($filename) {
        if (($fh = fopen($filename, 'r')) === FALSE) {
            throw new RuntimeException(error_get_last()['message']);
        }

        $i = 0;
        $errors = [];
        while (($line = fgets($fh)) !== FALSE) {
            $errors = array_merge($errors, $this->checkLine($line, ++$i));
        }

        fclose($fh);

        return $errors;
    }
}
