<?php

namespace App\Utils;


use \DateTime;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

class Validator
{
    private $isFailed;
    private $error_messages;
    private $rules;
    private $inputData;

    private function __construct()
    {
        $this->isFailed = false;
        $this->error_messages = array();
        $this->rules = array();
        $this->inputData = array();
    }

    /**
     * Create createValidatorInstance.
     *
     * @param  array  $rules
     */
    public static function makeValidator(array $input_data, array $rules, $should_perform_validation = false)
    {
        static  $validatorInstance;
        if (is_null($validatorInstance)) {
            $validatorInstance = new self();
        }
        if ($should_perform_validation) {
            $validatorInstance->performValidation($input_data, $rules);
        }

        return $validatorInstance;
    }

    /**
     * Setyer Rules.
     *
     * @param array $rules
     */
    public function setRules($rules)
    {
        if (!empty($rules)) {
            $this->rules = $rules;
        } else {
            throw new Exception("Rules can't be empty");
        }
    }

    /**
     * set inputData
     *  example data
     *  ['user_name' => 'value for field','password'=>'value for password field'].
     *
     * @param array $input_data [description]
     */
    private function setInputData($input_data)
    {
        $this->inputData = $input_data;
    }
    /**
     * Perorm validation on @var $rules
     *  example $rules array can be
     *  $rules = ['user_name' => ['required'],'password'=>['required','length:3']].
     */
    public function performValidation($input_data, $rules)
    {
        //Reset validation setup
        $this->setRules($rules);
        $this->setInputData($input_data);
        $this->isFailed(false);

        //start validation process
        foreach ($this->rules as $input_field_name => $field_rules) {
            $input_value = isset($this->inputData[$input_field_name]) ?
                $this->inputData[$input_field_name] : '';
            //apply all rules for input field
            foreach ($field_rules as $key => $rule) {
                $validation_type = ValidationType::getValidationType($rule);
                switch ($validation_type) {
                    case ValidationType::REQUIRED:
                        $trimmed_value =  $input_value;
                        if (!is_array($input_value))
                            $trimmed_value = trim($input_value);
                        if (empty($trimmed_value)) {
                            $this->error_messages[$input_field_name][] = $this->getSnakeCaseFieldName($input_field_name) . ' must not be empty';
                            $this->isFailed(true);
                        }
                        break;
                    case ValidationType::NUMERIC:
                        if (is_numeric($input_value) === FALSE) {
                            $this->error_messages[$input_field_name][] = $this->getSnakeCaseFieldName($input_field_name) . ' is not a numeric';
                            $this->isFailed(true);
                        }
                        break;
                    case ValidationType::INTEGER:
                        if (is_int($input_value) === FALSE) {
                            $this->error_messages[$input_field_name][] = $this->getSnakeCaseFieldName($input_field_name) . ' must be an integer value. Example 25';
                            $this->isFailed(true);
                        }
                        break;
                    case ValidationType::FLOAT:
                        if (is_float($input_value) === FALSE) {
                            $this->error_messages[$input_field_name][] = $this->getSnakeCaseFieldName($input_field_name) . ' must be an floating pint value. Example 12.25';
                            $this->isFailed(true);
                        }
                        break;
                    case ValidationType::MIN_LENGTH:
                        $length_limit = 0;
                        $rule_parts = explode(':', $rule);
                        if (count($rule_parts) !== 2) {
                            throw new Exception('Invalid Min Length Rules');
                        }
                        $length_limit = $rule_parts[1];
                        if (strlen($input_value) < $length_limit) {
                            $this->error_messages[$input_field_name][] = $this->getSnakeCaseFieldName($input_field_name) . ' must contains  at least ' . $length_limit . ' characters';
                            $this->isFailed(true);
                        }
                        break;
                    case ValidationType::MAX_LENGTH:
                        $length_limit = 0;
                        $rule_parts = explode(':', $rule);
                        if (count($rule_parts) !== 2) {
                            throw new Exception('Invalid Max Length Rules');
                        }
                        $length_limit = $rule_parts[1];
                        if (strlen($input_value) > $length_limit) {
                            $this->error_messages[$input_field_name][] = $this->getSnakeCaseFieldName($input_field_name) . ' length is not more than ' . $length_limit . ' characters';
                            $this->isFailed(true);
                        }
                        break;
                    case ValidationType::DATE_FORMAT:
                        $rule_parts = explode(':', $rule);
                        if (count($rule_parts) !== 2) {
                            throw new Exception('Invalid Date Format Length Rules');
                        }
                        $date_object = DateTime::createFromFormat($rule_parts[1], $input_value);
                        if (!($date_object && $date_object->format($rule_parts[1]) == $input_value)) {
                            $this->error_messages[$input_field_name][] = $this->getSnakeCaseFieldName($input_field_name) . ' must be in ' . $rule_parts[1] . ' format';
                            $this->isFailed(true);
                        }
                        break;
                    default:
                        throw new Exception($rule . ' not exist');
                        break;
                }
            } // end foreach ($field_rules.........)
        } //end foreach
    }

    /**
     * get Error messages after performing validation.
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->error_messages;
    }

    /**
     * Return whether validation failed or not.
     *
     * @return bool
     */
    public function isValidationFailed()
    {
        return $this->isFailed;
    }

    /**
     * Set isFailed fiedl if validation failed or not.
     *
     * @param bool $is_failed
     */
    private function isFailed($is_failed)
    {
        $this->isFailed = $is_failed;
    }
    /**
     * Prepare snake case name from field name
     * Exa. 'book_type' will be converted to 'Book Type'
     * @param  string $input_field_name
     * @return string
     */
    private function getSnakeCaseFieldName($input_field_name)
    {
        $snake_case_name = "";
        $snake_case_name = str_replace('_', ' ', $input_field_name);
        $snake_case_name = ucwords($snake_case_name);
        return $snake_case_name;
    }
    public function getErrorMessagesCollection()
    {
        return new Collection($this->error_messages);
    }
    /**
     * Send Validation errors if any 
     * @param  [type] $responseMessage [description]
     * @return [type]                  [description]
     */
    public function sendValidationErrorsIfAny($responseMessage)
    {
        if ($this->isValidationFailed()) {
            
            return new JsonResponse([
                'responseCode' => 400,
                'responseMessage' => $responseMessage,
                'responseContent' => $this->getErrorMessagesCollection()
            ], 400); // 401
            
        }

        return null;
    }
} // end of class
