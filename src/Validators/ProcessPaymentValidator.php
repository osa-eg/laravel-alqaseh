<?php

namespace Osama\AlQaseh\Validators;

use InvalidArgumentException;

class ProcessPaymentValidator
{
    /**
     * Validate payment processing parameters
     *
     * @param array $params
     * @throws InvalidArgumentException
     */
    public static function validate(array $params): void
    {
        // Validate required fields
        $requiredFields = [
            'CardNumber' => 'Card number is required',
            'Cvv' => 'CVV is required',
            'EXPMon' => 'Expiration month is required',
            'EXPYear' => 'Expiration year is required'
        ];

        foreach ($requiredFields as $field => $message) {
            if (!isset($params[$field]) || empty($params[$field])) {
                throw new InvalidArgumentException($message);
            }
        }

        // Validate card number format
        if (!preg_match('/^\d{13,19}$/', $params['CardNumber'])) {
            throw new InvalidArgumentException('Invalid card number format');
        }

        // Validate CVV format
        if (!preg_match('/^\d{3,4}$/', $params['Cvv'])) {
            throw new InvalidArgumentException('CVV must be 3 or 4 digits');
        }

        // Validate expiration month
        $month = (int)$params['EXPMon'];
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('Invalid expiration month (1-12)');
        }

        // Validate expiration year
        $currentYear = (int)date('y');
        $year = (int)$params['EXPYear'];
        if ($year < $currentYear || $year > $currentYear + 20) {
            throw new InvalidArgumentException('Invalid expiration year');
        }
    }
}