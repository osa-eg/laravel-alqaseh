<?php

namespace Osama\AlQaseh\Validators;

use InvalidArgumentException;

class CreatePaymentValidator
{
    /**
     * List of allowed transaction types
     */
    private const ALLOWED_TRANSACTION_TYPES = [
        'Retail',
        'Authorization',
        'Reversal',
        'CompleteSales'
    ];

    /**
     * Validate create payment request
     *
     * @param array $data
     * @throws InvalidArgumentException
     */
    public static function validate(array $data): void
    {
        self::validateRequiredFields($data);
        self::validateAmount($data['amount']);
        self::validateOrderId($data['order_id']);
        self::validateTransactionType($data['transaction_type']);
        self::validateOptionalFields($data);
    }

    /**
     * Validate required fields
     *
     * @param array $data
     * @throws InvalidArgumentException
     */
    private static function validateRequiredFields(array $data): void
    {
        $requiredFields = [
            'amount' => 'Amount is required',
            'currency' => 'Currency is required',
            'order_id' => 'Order ID is required',
            'description' => 'Description is required',
            'redirect_url' => 'Redirect URL is required',
            'transaction_type' => 'Transaction type is required'
        ];

        foreach ($requiredFields as $field => $message) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new InvalidArgumentException($message);
            }
        }
    }

    /**
     * Validate amount
     *
     * @param mixed $amount
     * @throws InvalidArgumentException
     */
    private static function validateAmount($amount): void
    {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException('Amount must be a positive number');
        }
    }

    /**
     * Validate order ID
     *
     * @param string $orderId
     * @throws InvalidArgumentException
     */
    private static function validateOrderId(string $orderId): void
    {
        if (strlen($orderId) > 250) {
            throw new InvalidArgumentException('Order ID must not exceed 250 characters');
        }
    }

    /**
     * Validate transaction type
     *
     * @param string $transactionType
     * @throws InvalidArgumentException
     */
    private static function validateTransactionType(string $transactionType): void
    {
        if (!in_array($transactionType, self::ALLOWED_TRANSACTION_TYPES)) {
            throw new InvalidArgumentException(
                'Invalid transaction type. Allowed types: ' . implode(', ', self::ALLOWED_TRANSACTION_TYPES)
            );
        }
    }

    /**
     * Validate optional fields
     *
     * @param array $data
     * @throws InvalidArgumentException
     */
    private static function validateOptionalFields(array $data): void
    {
        if (isset($data['email']) && strlen($data['email']) > 80) {
            throw new InvalidArgumentException('Email must not exceed 80 characters');
        }

        if (isset($data['nonce'])) {
            $nonceLength = strlen($data['nonce']);
            if ($nonceLength < 1 || $nonceLength > 64) {
                throw new InvalidArgumentException('Nonce must be between 1 and 64 characters');
            }
        }

        if (isset($data['p_sing'])) {
            $pSignLength = strlen($data['p_sing']);
            if ($pSignLength < 1 || $pSignLength > 256) {
                throw new InvalidArgumentException('P_sing must be between 1 and 256 characters');
            }
        }

        if (isset($data['custom_data']) && !is_array($data['custom_data'])) {
            throw new InvalidArgumentException('Custom data must be an object/array');
        }
    }
}