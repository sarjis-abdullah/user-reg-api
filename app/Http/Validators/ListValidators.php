<?php

namespace App\Http\Validators;

use JetBrains\PhpStorm\Pure;

class ListValidators
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
    public function validateList($attribute, $value, $parameters, $validator): bool
    {
        $valueArray = explode(',', $value);

        $callback = match ($parameters[0]) {
            'numeric' => function ($val) {
                return is_numeric($val);
            },
            'string' => function ($val) {
                return is_string($val);
            },
            'email' => function ($val) {
                return filter_var($val, FILTER_VALIDATE_EMAIL) !== false;
            }
        };

        $passedMapping = array_map($callback, $valueArray);

        return array_reduce($passedMapping, function ($carry, $item) {
            return $carry && $item;
        }, true);
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
    #[Pure] public function validationMessage($message, $attribute, $rule, $parameters): string
    {
        return sprintf('The %s field must be a valid comma separated list of %s values', $attribute, $parameters[0]);
    }
}
