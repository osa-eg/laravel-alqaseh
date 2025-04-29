<?php

namespace Osama\AlQaseh\Validators;

use InvalidArgumentException;

class PaymentHistoryValidator
{
    /**
     * Allowed payment statuses
     */
    private const PAYMENT_STATUSES = [
        'prepared',
        'revoked',
        'failed',
        'retried',
        'succeeded',
        'expired',
        'duplicated',
        'declined',
        'unknown'
    ];

    /**
     * Allowed transaction types
     */
    private const TRANSACTION_TYPES = [
        'Retail',
        'Authorization',
        'Reversal',
        'CompleteSales'
    ];

    /**
     * Allowed order by values
     */
    private const ORDER_BY = ['asc', 'desc'];

    /**
     * Validate payment history filters
     *
     * @param array $filters
     * @throws InvalidArgumentException
     */
    public static function validate(array $filters): void
    {
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'amount':
                    if (!is_numeric($value)) {
                        throw new InvalidArgumentException('Amount must be a number');
                    }
                    break;

                case 'limit':
                    if (!is_int($value) || $value < 0) {
                        throw new InvalidArgumentException('Limit must be a positive integer');
                    }
                    break;

                case 'offset':
                    if (!is_int($value) || $value < 0) {
                        throw new InvalidArgumentException('Offset must be a positive integer');
                    }
                    break;

                case 'order_by':
                    if (!in_array($value, self::ORDER_BY)) {
                        throw new InvalidArgumentException('Order by must be either asc or desc');
                    }
                    break;

                case 'payment_status':
                    if (!in_array($value, self::PAYMENT_STATUSES)) {
                        throw new InvalidArgumentException('Invalid payment status. Allowed values: ' . implode(', ', self::PAYMENT_STATUSES));
                    }
                    break;

                case 'transaction_type':
                    if (!in_array($value, self::TRANSACTION_TYPES)) {
                        throw new InvalidArgumentException('Invalid transaction type. Allowed values: ' . implode(', ', self::TRANSACTION_TYPES));
                    }
                    break;

                case 'from':
                case 'to':
                    if (!strtotime($value)) {
                        throw new InvalidArgumentException("Invalid date format for {$key}");
                    }
                    break;

                case 'language':
                    // Simple validation for language code
                    if (!preg_match('/^[a-z]{2}$/', strtolower($value))) {
                        throw new InvalidArgumentException('Invalid language code format');
                    }
                    break;
            }
        }
    }
}