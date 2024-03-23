<?php

namespace App\Http\Validators;

class DomainValidator
{
    /**
     * Register the list validation which validates a comma separated list of values
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return boolean
     */
    public function validateDomain($attribute, $value, $parameters, $validator): bool
    {
        return preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $value);
    }

    /**
     * Replace original message with our custom one
     *
     * @param $message
     * @param $attribute
     * @param $rule
     * @param $parameters
     * @return string
     */
    public function validationMessage($message, $attribute, $rule, $parameters): string
    {
        return "Not a valid domain";
    }
}
