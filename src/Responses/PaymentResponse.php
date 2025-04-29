<?php

namespace Osama\AlQaseh\Responses;

class PaymentResponse
{
    /**
     * The response data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new response instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Determine if the payment was successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return isset($this->data['success']) && $this->data['success'] === true;
    }

    /**
     * Get the transaction ID.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->data['data']['transactionId'] ?? null;
    }

    /**
     * Get the payment URL.
     *
     * @return string|null
     */
    public function getPaymentUrl()
    {
        return $this->data['data']['paymentUrl'] ?? null;
    }

    /**
     * Get the error message, if any.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->data['message'] ?? 'Unknown error';
    }

    /**
     * Get the error code, if any.
     *
     * @return string|int|null
     */
    public function getErrorCode()
    {
        return $this->data['code'] ?? null;
    }

    /**
     * Get the payment ID.
     *
     * @return string|null
     */
    public function getPaymentId()
    {
        return $this->data['payment_id'] ?? null;
    }

    /**
     * Get the payment token.
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->data['token'] ?? null;
    }

    /**
     * Get the error details.
     *
     * @return array|null
     */
    public function getError()
    {
        if (isset($this->data['err'])) {
            return [
                'message' => $this->data['err'],
                'code' => $this->data['error_code'] ?? null,
                'reference' => $this->data['reference_code'] ?? null
            ];
        }
        return null;
    }

    /**
     * Get the response data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}