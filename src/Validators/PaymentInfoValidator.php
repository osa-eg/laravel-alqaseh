<?php

namespace Osama\AlQaseh\Validators;

use InvalidArgumentException;

class PaymentInfoValidator
{
    /**
     * Validate payment info request parameters
     *
     * @param string $id
     * @throws InvalidArgumentException
     */
    public static function validate(string $id): void
    {
        if (empty(trim($id))) {
            throw new InvalidArgumentException('Payment ID is required');
        }
    }
}