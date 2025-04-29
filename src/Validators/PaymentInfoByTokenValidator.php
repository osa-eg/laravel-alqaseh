<?php

namespace Osama\AlQaseh\Validators;

use InvalidArgumentException;

class PaymentInfoByTokenValidator
{
    /**
     * Validate payment info request parameters
     *
     * @param string $token
     * @throws InvalidArgumentException
     */
    public static function validate(string $token): void
    {
        if (empty(trim($token))) {
            throw new InvalidArgumentException('Payment ID is required');
        }
    }
}