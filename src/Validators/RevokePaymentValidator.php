<?php

namespace Osama\AlQaseh\Validators;

use InvalidArgumentException;

class RevokePaymentValidator
{
    /**
     * Validate revoke payment request parameters
     *
     * @param array $params
     * @throws InvalidArgumentException
     */
    public static function validate(array $params): void
    {
        if (!isset($params['payment_id']) || empty($params['payment_id'])) {
            throw new InvalidArgumentException('Payment ID is required');
        }

        if (strlen($params['payment_id']) > 250) {
            throw new InvalidArgumentException('Payment ID must not exceed 250 characters');
        }

        // Details is optional but if provided should be string
        if (isset($params['details']) && !is_string($params['details'])) {
            throw new InvalidArgumentException('Details must be a string');
        }
    }
}