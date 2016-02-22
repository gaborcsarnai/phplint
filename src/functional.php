<?php
/**
 * Checks file against quality criteria. This function is a rapper around the
 * {@see PHPLint} class.
 *
 * @param string $filename  Filename to process.
 * @param array $rules  Key-value pairs to extend default rules. Array keys are
 * regexp patterns applied to each line, values are the rule descriptions for
 * each pattern.
 * @return array|bool  Returns an array of arrays with the following elements:
 * (0) rule description, (1) line number, (2) position.  Returns <tt>FALSE</tt>
 * on file open error.
 * @author Gabor Csarnai <gabor@antavo.com>
 */
function phplint($filename, array $rules = []) {
    try {
        return (new PHPLint)->addRules($rules)->process($filename);
    } catch (RuntimeException $e) {
        return FALSE;
    }
}
