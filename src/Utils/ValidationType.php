<?php

namespace App\Utils;


use ReflectionClass;

class ValidationType
{
    const REQUIRED = 'required';
    const NUMERIC = 'numeric';
    const INTEGER = "integer";
    const FLOAT = "float";
    const DATE = 'date';
    const DATE_FORMAT = 'date_format'; // date_format:dd-mm-yyy  specify format after :
    const EMAIL = 'email';
    const RANGE = 'range'; // range:34,70 it should be number or floating point number and between given limit
    const MIN_LENGTH = 'min_length'; // min_length:3 used for strings
    const MAX_LENGTH = 'max_length'; // max_length:3 used for strings


    /**
     * get Actual Rule name
     * @param  string $rule_string
     * @return CONST
     */
    public static function getValidationType($rule_string)
    {
        $original_rule_name = '';
        $reflectionClass = new ReflectionClass(__CLASS__);
        $class_contants = $reflectionClass->getConstants();
        foreach ($class_contants as $key => $class_contant) {
            $pos = strpos($rule_string, $class_contant);
            if ($pos !== false) {
                $original_rule_name = $class_contant;
                break;
            }
        }

        return $original_rule_name;
    }
}
