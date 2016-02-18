<?php

/**
 * Intended for Validation of Values in the whole application. Does not sanitize any values. If the value is not correct, false is returned.
 *
 * Recommended: Raise notice if expected validation is not working in the classes used by. Validator does not log any results.
 *
 * @author: Blackbam 2014
 *
 */
class Validator {


    /**
     * Checks if a string is valid to the given options.
     *
     * min_len/min_length: Minimum length of string
     * max_len/max_length: Maximum length of string
     * nemp/not_empty: Checks is string contains at least one character
     * slug: Checks is string is a valid slug (only lowercase characters, underscore, minus and letters, must start with letter)
     * email: Checks is string is valid email address
     * color/hex_color: Checks if string is valid hex color (f.e. #813636)
     */
    public static function validateString($val, $options = array())
    {

        if (!is_string($val)) {
            return false;
        }

        if (!is_array($options)) {
            throw new Exception("Invalid options passed to Validator. Check your source code!");
            return false;
        }

        foreach ($options as $option => $req) {

            switch (strtoupper(trim($option))):

                case "MIN_LEN":
                case "MIN_LENGTH": {
                    if (strlen($val) < intval($req)) {
                        return false;
                    }
                    break;
                }

                case "MAX_LEN":
                case "MAX_LENGTH": {
                    if (strlen($val) > intval($req)) {
                        return false;
                    }
                    break;
                }

                case "NEMP":
                case "NOT_EMPTY": {
                    if (strlen($val) < 1) {
                        return false;
                    }
                    break;
                }

                case "SLUG": {
                    if (filter_var($val, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-z-][_a-z0-9-]+$/"))) === false) {
                        return false;
                    }
                    break;
                }

                case "EMAIL": {
                    if (filter_var($val, FILTER_VALIDATE_EMAIL) === false) {
                        return false;
                    }
                    break;
                }

                case "COLOR":
                case "HEX_COLOR": {
                    if (preg_match('/^[a-f0-9]{6}$/i', $val) != 1) {
                        return false;
                    }
                    break;
                }

                case "HEX": {
                    if (filter_var($val, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[0-9A-Fa-f]+$/"))) === false) {
                        return false;
                    }
                    break;
                }

                case "DATE": {
                    $d = DateTime::createFromFormat('Y-m-d', $val);
                    if (!($d && $d->format('Y-m-d') == $val)) {
                        return false;
                    }
                    break;
                }
                case "DATETIME":
                case "DATE_TIME":
                    $d = DateTime::createFromFormat('Y-m-d H:i:s', $val);
                    if (!($d && $d->format('Y-m-d H:i:s') == $val)) {
                        return false;
                    }
                    break;
                case "IP": {
                    if (false === filter_var($val, FILTER_VALIDATE_IP)) {
                        return false;
                    }
                    break;
                }
                case "SESSION_ID": {
                    if ($val == "") {
                        return false;
                    }
                    break;
                }
                case "ZIP": {
                    if ($val == "" || !ctype_alnum($val)) {
                        return false;
                    }
                    break;
                }
                // phone number
                case "PHONE": {
                    if (preg_match('/^\+[0-9 ]*$/', $val) != 1 || strlen($val) < 3 || strlen($val) > 40) {
                        return false;
                    }
                    break;
                }
                // check if valid url
                case "URL": {
                    if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $val) != 1) {
                        return false;
                    }
                    break;
                }
                // check if valid path
                case "PATH": {
                    if (preg_match('#^(((/{1}\.{1})?[a-zA-Z0-9 _-]+/?)*)/{1}$#', $val) < 1) {
                        return false;
                    }
                    break;
                }
                // check if all characters in the string are valid in a url
                case "URL_CHARS": {
                    if (preg_match("/^[a-zA-Z0-9\$\-\_\.\+\!\*\'\(\)\,]*$/", $val) != 1) {
                        return false;
                    }
                    break;
                }
                case "HTML_ATTRIB": {
                    if (preg_match("/^[a-zA-Z_:][-a-zA-Z0-9_:.]*$/", $val) != 1) {
                        return false;
                    }
                    break;
                }
                case "ALNUM":
                case "ALPHANUMERIC":
                    if (preg_match('/^[a-zA-Z0-9]+$/', $val) < 1) {
                        return false;
                    }
                    break;
                // check if string is valid mime type
                case "MIME":
                case "MIME_TYPE":
                    if (preg_match('#^[-\w]+/[-\w]+$#', $val) < 1) {
                        return false;
                    }
                    break;
                default:
                    return false;
            endswitch;
        }
        return true;
    }


    /**
     * Checks if an integer is valid to the given options.
     *
     * min/min_value: The minimum value
     * max/max_value: The maximum value
     * noz/not_zero: Integer not equal to zero
     * positive: Integer bigger than zero
     */
    public static function validateInt($val, $options)
    {

        if (!is_int($val)) {
            return false;
        }

        foreach ($options as $option => $req) {

            switch (strtoupper(trim($option))):

                case "MIN":
                case "MIN_VALUE": {
                    if ($val < intval($req)) {
                        return false;
                    }
                    break;
                }

                case "MAX":
                case "MAX_VALUE": {
                    if ($val > intval($req)) {
                        return false;
                    }
                    break;
                }

                case "NOZ":
                case "NOT_ZERO": {
                    if ($val === 0) {
                        return false;
                    }
                    break;
                }

                case "POSITIVE": {
                    if ($val < 1) {
                        return false;
                    }
                    break;
                }

                case "TIME":
                case "TIMESTAMP": {
                    if ($val < 0) {
                        return false;
                    }
                    break;
                }
                default:
                    return false;
            endswitch;
        }
        return true;
    }


    public static function validateFloat($val, $options)
    {

        if (!is_float($val)) {
            return false;
        }

        foreach ($options as $option => $req) {

            switch (strtoupper(trim($option))):

                case "MIN":
                case "MIN_VALUE": {
                    if ($val < intval($req)) {
                        return false;
                    }
                    break;
                }

                case "MAX":
                case "MAX_VALUE": {
                    if ($val > intval($req)) {
                        return false;
                    }
                    break;
                }

                case "NOZ":
                case "NOT_ZERO": {
                    if ($val === 0.0) {
                        return false;
                    }
                    break;
                }

                case "POSITIVE": {
                    if (!($val > 0.0)) {
                        return false;
                    }
                    break;
                }

                case "MICROTIME": {
                    if ($val < 0.0) {
                        return false;
                    }
                    break;
                }

                default:
                    return false;
            endswitch;
        }
        return true;
    }
}