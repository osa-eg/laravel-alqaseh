<?php

namespace Osama\AlQaseh;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Osama\AlQaseh\Responses\PaymentResponse createPayment(float $amount, string $currency, string $orderId, string $description, string $redirectUrl, ?string $transactionType = null, ?string $country = null, ?array $customData = null, ?string $email = null)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse getPaymentInfo(string $paymentId)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse getPaymentHistory(array $filters, int $limit = 50, string $orderBy = 'desc')
 * @method static string downloadPaymentHistory(array $filters)
 * @method static void retryPayment(string $paymentId, string $details)
 * @method static void revokePayment(string $paymentId, string $details)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse processPayment(string $token, string $cardNumber, string $cvv, string $expiryMonth, string $expiryYear)
 */
class AlQasehFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'alqaseh';
    }
}