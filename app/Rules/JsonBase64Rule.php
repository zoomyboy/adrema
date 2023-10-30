<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class JsonBase64Rule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            return false;
        }

        if (false === base64_decode($value, true)) {
            return false;
        }

        $decoded = rawurldecode(base64_decode($value, true));

        if (base64_encode(rawurlencode($decoded)) !== $value) {
            return false;
        }

        if (!is_array(json_decode($decoded, true))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
