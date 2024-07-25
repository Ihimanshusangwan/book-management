<?php

class Validator {
    public static function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $ruleset) {
            $rulesArray = explode('|', $ruleset);

            foreach ($rulesArray as $rule) {
                if ($rule === 'required' && (!isset($data[$field]) || empty($data[$field]))) {
                    $errors[] = 'The ' . $field . ' field is required.';
                }

                if ($rule === 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'The ' . $field . ' field must be a valid email address.';
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = explode(':', $rule)[1];
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[] = 'The ' . $field . ' field must be at least ' . $min . ' characters long.';
                    }
                }

                if (strpos($rule, 'max:') === 0) {
                    $max = explode(':', $rule)[1];
                    if (isset($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[] = 'The ' . $field . ' field must be no more than ' . $max . ' characters long.';
                    }
                }

                if ($rule === 'alpha' && isset($data[$field]) && !ctype_alpha($data[$field])) {
                    $errors[] = 'The ' . $field . ' field must contain only alphabetic characters.';
                }
                
                if ($rule === 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[] = 'The ' . $field . ' field must contain only numeric characters.';
                }
            }
        }

        return $errors;
    }
}
