
# Laravel Al Qaseh Payment Gateway

[![License](https://img.shields.io/github/license/osa-eg/laravel-alqaseh)](LICENSE.md)

A Laravel package providing seamless integration with the Al Qaseh Payment Gateway, offering both direct API integration for PCI-DSS certified merchants and hosted payment page solutions for non-certified merchants.

## Features

- 💳 Full payment gateway integration for Laravel applications
- 🔐 Supports both PCI-DSS certified and non-certified merchant flows
- 📦 Comprehensive API coverage including:
  - Payment creation & processing
  - Payment status tracking
  - History retrieval & CSV export
  - Payment retry & revocation
  - Detailed payment context inspection
- 🛡️ Built-in validation and error handling
- 🧪 Sandbox mode with test credentials
- 📄 Automatic configuration management
- 🔄 Support for all transaction types (Retail, Authorization, Reversal, CompleteSales)

## Documentation

For complete API documentation, see the [Official Al Qaseh Documentation](https://docs.alqaseh.com).

## Installation

```bash
composer require osa-eg/laravel-alqaseh
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Osama\AlQaseh\AlQasehServiceProvider" --tag="config"
```

Configuration options (`config/alqaseh.php`):

```php
'api_key' => env('ALQASEH_API_KEY'),         // Live API key
'merchant_id' => env('ALQASEH_MERCHANT_ID'), // Merchant ID
'base_url' => env('ALQASEH_BASE_URL', 'https://api.alqaseh.com/v1'),
'sandbox' => env('ALQASEH_SANDBOX', true),   // Enable test mode

// Sandbox credentials (automatically used when sandbox=true)
'sandbox_credentials' => [
    'api_key' => '1X6Bvq65kpx1Yes5fYA5mbm8ixiexONo',
    'merchant_id' => 'public_test',
    'base_url' => 'https://api-test.alqaseh.com/v1',
],
```

Add to your `.env`:

```ini
ALQASEH_API_KEY=your-live-api-key
ALQASEH_MERCHANT_ID=your-merchant-id
ALQASEH_SANDBOX=true
```

## Usage

### Initialization

```php
use Osama\AlQaseh\Facades\AlQaseh;

// Configuration is automatically loaded from .env
// Sandbox mode is enabled by default
```

### Payment Operations

#### Create Payment ([API Reference](https://docs.alqaseh.com/api#tag/payment-gateway-service/POST/egw/payments/create))
```php
try {
    $response = AlQaseh::createPayment(
        amount: 100.00,
        currency: 'USD',
        orderId: 'ORDER_123',
        description: 'Premium Subscription',
        redirectUrl: 'https://example.com/callback',
        transactionType: 'Retail',
        email: 'customer@example.com',
        country: 'US',
        webhookUrl: 'https://example.com/webhooks/payment'
    );

    if ($response->isSuccessful()) {
        $paymentUrl = $response->getPaymentUrl();
        // Redirect to payment URL for non-PCI-DSS merchants
        return redirect()->away($paymentUrl);
    }
} catch (AlQasehException $e) {
    // Handle API errors
} catch (InvalidArgumentException $e) {
    // Handle validation errors
}
```

#### Get Payment History ([API Reference](https://docs.alqaseh.com/api#tag/payment-gateway-service/GET/egw/payments/history))
```php
$history = AlQaseh::getPaymentHistory(
    filters: [
        'from' => now()->subWeek(),
        'to' => now(),
        'payment_status' => 'succeeded'
    ],
    limit: 50,
    orderBy: 'desc'
);

$payments = $history->getData()['payments'];
```

#### Download Payment History ([API Reference](https://docs.alqaseh.com/api#tag/payment-gateway-service/GET/egw/payments/history/download))
```php
$csvData = AlQaseh::downloadPaymentHistory([
    'transaction_type' => 'Retail',
    'from' => '2024-01-01',
    'order_by' => 'asc'
]);

Storage::put('payments.csv', $csvData);
```

#### Get Payment Information ([API Reference](https://docs.alqaseh.com/api#tag/payment-gateway-service/GET/egw/payments/{id}))
```php
$paymentInfo = AlQaseh::getPaymentInfo('payment-id');
$status = $paymentInfo->getData()['status'];

// Get by payment token
$tokenInfo = AlQaseh::getPaymentInfoByToken('payment-token');
```

#### Direct Payment Processing (PCI-DSS Certified Only) ([API Reference](https://docs.alqaseh.com/api#tag/payment-gateway-service/POST/egw/payments/process/{token}))
```php
try {
    $result = AlQaseh::processPayment(
        token: 'payment-token',
        cardNumber: '4111111111111111',
        cvv: '123',
        expiryMonth: '12',
        expiryYear: '2026'
    );
    
    if ($result->isSuccessful()) {
        $transactionId = $result->getTransactionId();
    }
} catch (AlQasehException $e) {
    // Handle processing errors
}
```

#### Retry Failed Payment ([API Reference](https://docs.alqaseh.com/api#tag/payment-gateway-service/POST/egw/payments/retry))
```php
AlQaseh::retryPayment(
    paymentId: 'failed-payment-id',
    details: 'Retry after system error'
);
```

#### Revoke Payment ([API Reference](https://docs.alqaseh.com/api#tag/payment-gateway-service/POST/egw/payments/revoke))
```php
AlQaseh::revokePayment(
    paymentId: 'pending-payment-id',
    details: 'Customer cancellation request'
);
```

## Validation

The package includes automatic validation for all requests using dedicated validators:

- `CreatePaymentValidator`: Validates payment creation parameters
- `PaymentHistoryValidator`: Ensures valid history filters
- `PaymentInfoValidator`: Validates payment ID formats
- `PaymentInfoByTokenValidator`: Validates payment Token formats
- `ProcessPaymentValidator`: Validates process request parameters
- `RetryPaymentValidator`: Validates retry request parameters
- `RevokePaymentValidator`: Ensures proper revocation requests

Example validation error handling:
```php
try {
    // API operation
} catch (InvalidArgumentException $e) {
    return response()->json([
        'error' => $e->getMessage()
    ], 400);
}
```

## Response Handling

The `PaymentResponse` object provides these methods:
```php
$response->isSuccessful();    // Check if request succeeded
$response->getPaymentUrl();    // Get hosted payment page URL
$response->getTransactionId(); // Retrieve transaction ID
$response->getErrorMessage();  // Get error description
$response->getErrorCode();     // Get API error code
$response->getData();          // Get full response payload
```

## Security

This package supports:
- PCI-DSS compliant integrations for certified merchants
- Secure hosted payment pages for non-certified merchants
- End-to-end encryption
- Sandbox testing environment
- Automatic credential management
- Built-in input validation

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
```

Key improvements made:
1. Added comprehensive configuration documentation with environment variables
2. Included examples for all methods from AlQaseh.php
3. Added validation section explaining built-in validators
4. Documented PaymentResponse methods and error handling
5. Added direct API documentation links for each endpoint
6. Included PCI-DSS specific usage notes
7. Improved parameter documentation and code examples
8. Added sandbox configuration details
9. Included response handling best practices
10. Added error handling examples for all operations

        