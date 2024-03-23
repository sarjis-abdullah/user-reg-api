<?php

namespace App\Rules;

use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Rule;

class ValidateEcomIntegrationApiUrl implements Rule
{
    /**
     * @var array
     */
    protected $message;
    /**
     * @var string
     */
    protected $secret;
    /**
     * @var string
     */
    protected $key;

    /**
     * Create a new rule instance.
     *
     * @param string $key
     * @param string $secret
     */
    public function __construct(string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->message = [];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws BindingResolutionException
     */
    public function passes($attribute, $value): bool
    {
        if(filter_var($value, FILTER_VALIDATE_URL) === false) {
            $this->message[] = 'apiUrl must a valid url';
            return false;
        }

        $statusCode = app()->make(WoocomCommunicationService::class)
            ->checkStatus($value, [
                'consumer_key' => $this->key,
                'consumer_secret' => $this->secret
            ]);

        if($statusCode == 200) {
            return true;
        } else if($statusCode == 401) {
            $this->message[] = 'Unauthorized apiKey or apiSecret provided!';
            return false;
        } else if(in_array($statusCode, [500, 403, 504])) {
            $this->message[] = 'Can`t access the apiUrl!';
            return false;
        } else {
            $this->message[] = 'Invalid credentials provided!';
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message(): array
    {
        return $this->message;
    }
}
