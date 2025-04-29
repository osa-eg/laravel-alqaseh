<?php

namespace Osama\AlQaseh;

use Illuminate\Support\Facades\Http;
use Osama\AlQaseh\Exceptions\AlQasehException;
use Osama\AlQaseh\Responses\PaymentResponse;
use Osama\AlQaseh\Validators\CreatePaymentValidator;
use Osama\AlQaseh\Enums\AlQasehEndpoint;
use Osama\AlQaseh\Validators\DownloadPaymentHistoryValidator;
use Osama\AlQaseh\Validators\PaymentHistoryValidator;
use Osama\AlQaseh\Validators\PaymentInfoByTokenValidator;
use Osama\AlQaseh\Validators\PaymentInfoValidator;
use Osama\AlQaseh\Validators\ProcessPaymentValidator;
use Osama\AlQaseh\Validators\RetryPaymentValidator;
use Osama\AlQaseh\Validators\RevokePaymentValidator;

class AlQaseh
{
    /**
     * API Key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Merchant ID
     *
     * @var string
     */
    protected $merchantId;

    /**
     * Base URL
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Sandbox mode
     *
     * @var bool
     */
    protected $sandbox;

    /**
     * Constructor
     *
     * @param string|null $apiKey
     * @param string|null $merchantId
     * @param string|null $baseUrl
     * @param bool $sandbox
     */
    public function __construct(
        $apiKey = null,
        $merchantId = null,
        $baseUrl = null,
        $sandbox = true
    ) {
        $this->sandbox = $sandbox;

        // Use sandbox credentials by default if sandbox is true
        $this->apiKey = $sandbox ? '1X6Bvq65kpx1Yes5fYA5mbm8ixiexONo' : $apiKey;
        $this->merchantId = $sandbox ? 'public_test' : $merchantId;
        $this->baseUrl = $sandbox ? 'https://api-test.alqaseh.com/v1' : ($baseUrl ?? 'https://api.alqaseh.com/v1');
    }

    /**
     * Get API headers
     *
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'API-Key' => $this->apiKey,
            'X-Merchant-ID' => $this->merchantId,
            'Authorization' => 'Basic ' . base64_encode($this->merchantId . ':' . $this->apiKey)
        ];
    }

    /**
     * Create a new payment request
     *
     * @param float  $amount          Required. The payment amount
     * @param string $currency        Required. The payment currency code
     * @param string $orderId         Required. Unique order identifier (max: 250 characters)
     * @param string $description     Required. Payment description
     * @param string $redirectUrl     Required. URL to redirect after payment completion
     * @param string $transactionType Optional. Type of transaction (default: 'Retail')
     *                               Allowed values: Retail|Authorization|Reversal|CompleteSales
     * @param string|null $country    Optional. Country code
     * @param array|null $customData  Optional. Additional custom data as key-value pairs
     * @param string|null $email      Optional. Customer email address (max: 80 characters)
     * @param string|null $nonce      Optional. Unique identifier for request (1-64 characters)
     * @param string|null $pSign      Optional. Payment signature (1-256 characters)
     * @param string|null $webhookUrl Optional. URL for payment notifications
     * @return PaymentResponse Returns payment response object containing payment details and token
     * @throws AlQasehException When API request fails or validation errors occur
     * @throws InvalidArgumentException When invalid parameters are provided
     */
    public function createPayment(
        float $amount,
        string $currency,
        string $orderId,
        string $description,
        string $redirectUrl,
        string $transactionType = 'Retail',
        ?string $country = null,
        ?array $customData = null,
        ?string $email = null,
        ?string $nonce = null,
        ?string $pSign = null,
        ?string $webhookUrl = null
    ) {
        $payload = [
            'amount' => $amount,
            'currency' => $currency,
            'order_id' => $orderId,
            'description' => $description,
            'redirect_url' => $redirectUrl,
            'transaction_type' => $transactionType
        ];

        // Add optional parameters if provided
        if ($country) {
            $payload['country'] = $country;
        }

        if ($customData) {
            $payload['custom_data'] = $customData;
        }

        if ($email) {
            $payload['email'] = $email;
        }

        if ($nonce) {
            $payload['nonce'] = $nonce;
        }

        if ($pSign) {
            $payload['p_sing'] = $pSign;
        }

        if ($webhookUrl) {
            $payload['webhook_url'] = $webhookUrl;
        }

        // Validate the payload
        CreatePaymentValidator::validate($payload);

        return $this->sendRequest('POST', AlQasehEndpoint::CREATE_PAYMENT->value, $payload);
    }

    /**
     * Get payment history
     *
     * @param array $filters {
     *     Optional. Array of filters to apply to the payment history.
     *     
     *     @var float    $amount          Optional. Filter by payment amount
     *     @var string   $approval        Optional. Filter by approval code
     *     @var string   $currency        Optional. Filter by currency
     *     @var string   $from            Optional. Start date for filtering
     *     @var string   $to              Optional. End date for filtering
     *     @var string   $language        Optional. Response language (default: 'en')
     *     @var string   $order_id        Optional. Filter by order ID
     *     @var string   $payment_id      Optional. Filter by payment ID
     *     @var string   $payment_status  Optional. Filter by payment status (prepared|revoked|failed|retried|succeeded|expired|duplicated|declined|unknown)
     *     @var string   $rc              Optional. Filter by response code
     *     @var string   $rrn             Optional. Filter by reference number
     *     @var string   $transaction_type Optional. Filter by transaction type (Retail|Authorization|Reversal|CompleteSales)
     * }
     * @param int    $limit       Optional. Number of records to return (default: 20)
     * @param int    $offset      Optional. Number of records to skip (default: 0)
     * @param string $orderBy     Optional. Sort order (asc|desc) (default: 'desc')
     * @param string $orderField  Optional. Field to sort by (default: 'created_at')
     * @return PaymentResponse
     * @throws AlQasehException
     * @throws InvalidArgumentException When invalid filter parameters are provided
     */
    public function getPaymentHistory(array $filters = [], $limit = 20, $offset = 0, $orderBy = 'desc', $orderField = 'created_at')
    {
        $queryParams = array_merge([
            'limit' => $limit,
            'offset' => $offset,
            'order_by' => $orderBy,
            'order_field' => $orderField
        ], $filters);

        // Validate query parameters
        PaymentHistoryValidator::validate($queryParams);

        return $this->sendRequest('GET', AlQasehEndpoint::PAYMENT_HISTORY->value, [], $queryParams);
    }

    /**
     * Download payment history as CSV
     *
     * @param array $filters {
     *     Optional. Array of filters to apply to the payment history.
     *     
     *     @var float    $amount          Optional. Filter by payment amount
     *     @var string   $approval        Optional. Filter by approval code
     *     @var string   $currency        Optional. Filter by currency
     *     @var string   $from            Optional. Start date for filtering
     *     @var string   $to              Optional. End date for filtering
     *     @var string   $language        Optional. Response language (default: 'en')
     *     @var string   $order_id        Optional. Filter by order ID
     *     @var string   $payment_id      Optional. Filter by payment ID
     *     @var string   $payment_status  Optional. Filter by payment status (prepared|revoked|failed|retried|succeeded|expired|duplicated|declined|unknown)
     *     @var string   $rc              Optional. Filter by response code
     *     @var string   $rrn             Optional. Filter by reference number
     *     @var string   $transaction_type Optional. Filter by transaction type (Retail|Authorization|Reversal|CompleteSales)
     * }
     * @param int    $limit       Optional. Number of records to return (default: 20)
     * @param int    $offset      Optional. Number of records to skip (default: 0)
     * @param string $orderBy     Optional. Sort order (asc|desc) (default: 'desc')
     * @param string $orderField  Optional. Field to sort by (default: 'created_at')
     * @return PaymentResponse Returns CSV formatted payment history
     * @throws AlQasehException When API request fails
     * @throws InvalidArgumentException When invalid filter parameters are provided
     */
    public function downloadPaymentHistory(array $filters = [], $limit = 20, $offset = 0, $orderBy = 'desc', $orderField = 'created_at')
    {
        $queryParams = array_merge([
            'limit' => $limit,
            'offset' => $offset,
            'order_by' => $orderBy,
            'order_field' => $orderField
        ], $filters);

        // Validate query parameters
        DownloadPaymentHistoryValidator::validate($queryParams);

        return $this->sendRequest('GET', AlQasehEndpoint::DOWNLOAD_PAYMENT_HISTORY->value, [], $queryParams);
    }

    /**
     * Get detailed payment context information
     *
     * @param string $id Required. The payment context ID to retrieve
     * @return PaymentResponse Returns payment context details including:
     *         - amount: Payment amount
     *         - approval_code: Transaction approval code
     *         - country: Country code
     *         - created_at: Creation timestamp
     *         - currency: Payment currency
     *         - custom_data: Custom data object
     *         - payment_status_histories: Array of status changes
     *         - transaction_type: Retail/Authorization/Reversal/etc
     *         - webhook_url: Configured webhook URL
     * @throws AlQasehException When API request fails
     * @throws InvalidArgumentException When invalid ID is provided
     */
    public function getPaymentInfo(string $id)
    {
        // Validate the ID
        PaymentInfoValidator::validate($id);

        return $this->sendRequest(
            'GET',
            AlQasehEndpoint::PAYMENT_DETAILS->getEndpoint(['id' => $id])
        );
    }

    /**
     * Get payment information by ID
     *
     * @param string $id Required. The payment ID to retrieve information for
     * @return PaymentResponse Returns payment response object containing:
     *         - amount: The payment amount
     *         - approval_code: The approval code if payment is approved
     *         - country: The country code
     *         - created_at: Creation timestamp
     *         - currency: The payment currency
     *         - custom_data: Any custom data associated with the payment
     *         - description: Payment description
     *         - merchant_email: Merchant email address
     *         - order_id: The original order ID
     *         - payment_id: The payment ID
     *         - payment_status: Current payment status
     *         - payment_status_histories: Array of status change history
     *         - transaction_type: Type of transaction
     *         - webhook_url: Webhook URL if configured
     * @throws AlQasehException When API request fails
     * @throws InvalidArgumentException When invalid ID is provided
     */
    public function getPaymentInfoByToken(string $token)
    {
        // Validate the ID
        PaymentInfoByTokenValidator::validate($token);

        return $this->sendRequest('GET', AlQasehEndpoint::PAYMENT_INFO_BY_TOKEN->getEndpoint(['token' => $token]));
    }

    /**
     * Process tokenized payment with card details
     *
     * @param string $token Required. Payment token from createPayment response
     * @param string $cardNumber Required. Card number (13-19 digits)
     * @param string $cvv Required. Card verification value (3-4 digits)
     * @param string $expMonth Required. Expiration month (1-12)
     * @param string $expYear Required. Expiration year (YYYY format)
     * @return PaymentResponse Returns payment response object containing:
     *         - transaction_id: Unique transaction identifier
     *         - approval_code: Bank authorization code
     *         - amount: Processed amount
     *         - currency: Transaction currency
     *         - timestamp: Processing timestamp
     *         - card_last4: Last 4 digits of card
     *         - card_type: Visa/Mastercard/etc
     *         - payment_status: succeeded|failed|pending
     *         - error_code: API error code if failed
     *         - error_message: Human-readable error description
     * @throws AlQasehException When API request fails
     * @throws InvalidArgumentException When invalid parameters are provided
     */
    public function processPayment($token, $cardNumber, $cvv, $expMonth, $expYear)
    {
        $payload = [
            'CardNumber' => $cardNumber,
            'Cvv' => $cvv,
            'EXPMon' => $expMonth,
            'EXPYear' => $expYear
        ];

        // Validate the payload
        ProcessPaymentValidator::validate($payload);

        return $this->sendRequest('POST', AlQasehEndpoint::PROCESS_PAYMENT->getEndpoint(['token' => $token]), $payload);
    }


    /**
     * Retry a failed or expired payment
     *
     * @param string $paymentId Required. The payment ID to retry (max: 250 characters)
     * @param string|null $details Optional. Additional details about the retry
     * @return PaymentResponse Returns payment response object containing retry status
     * @throws AlQasehException When API request fails
     * @throws InvalidArgumentException When invalid parameters are provided
     */
    public function retryPayment(string $paymentId, ?string $details = null)
    {
        $payload = [
            'payment_id' => $paymentId
        ];

        if ($details !== null) {
            $payload['details'] = $details;
        }

        // Validate the payload
        RetryPaymentValidator::validate($payload);

        return $this->sendRequest('POST', AlQasehEndpoint::RETRY_PAYMENT->value, $payload);
    }

    /**
     * Revoke a payment before processing
     *
     * @param string $paymentId Required. The payment ID to revoke (max: 250 characters)
     * @param string|null $details Optional. Additional details about the revocation
     * @return PaymentResponse Returns payment response object containing:
     *         - amount: The payment amount
     *         - currency: The payment currency
     *         - custom_data: Any custom data associated with the payment
     *         - order_id: The original order ID
     *         - payment_id: The payment ID
     *         - payment_status: The current payment status
     * @throws AlQasehException When API request fails
     * @throws InvalidArgumentException When invalid parameters are provided
     */
    public function revokePayment(string $paymentId, ?string $details = null)
    {
        $payload = [
            'payment_id' => $paymentId
        ];

        if ($details !== null) {
            $payload['details'] = $details;
        }

        // Validate the payload
        RevokePaymentValidator::validate($payload);

        return $this->sendRequest('POST', AlQasehEndpoint::REVOKE_PAYMENT->value, $payload);
    }


     /**
     * Send request to API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $queryParams
     * @return PaymentResponse
     * @throws AlQasehException
     */
    protected function sendRequest($method, $endpoint, array $data = [], array $queryParams = [])
    {
        $url = rtrim($this->baseUrl, '/') . $endpoint;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        try {
            $options = [
                'verify' => !$this->sandbox,
                'headers' => $this->getHeaders()
            ];

            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = Http::withOptions($options)
                ->{strtolower($method)}($url);

            $responseData = $response->json();

            if ($response->failed()) {
                throw new AlQasehException(
                    $responseData['err'] ?? 'Unknown error occurred',
                    $response->status(),
                    $responseData['reference_code'] ?? null
                );
            }

            return new PaymentResponse($responseData);
        } catch (\Exception $e) {
            if ($e instanceof AlQasehException) {
                throw $e;
            }

            throw new AlQasehException('Connection error: ' . $e->getMessage(), 0, $e);
        }
    }
}
