<?php

namespace Osama\AlQaseh;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Osama\AlQaseh\Responses\PaymentResponse createPayment(float $amount, string $currency, string $orderId, string $description, string $redirectUrl, ?string $transactionType = null, ?string $country = null, ?array $customData = null, ?string $email = null)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse getPaymentInfo(string $id)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse getPaymentInfoByToken(string $token)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse getPaymentHistory(array $filters = [], int $limit = 20, int $offset = 0, string $orderBy = 'desc', string $orderField = 'created_at')
 * @method static \Osama\AlQaseh\Responses\PaymentResponse downloadPaymentHistory(array $filters = [], int $limit = 20, int $offset = 0, string $orderBy = 'desc', string $orderField = 'created_at')
 * @method static \Osama\AlQaseh\Responses\PaymentResponse processPayment(string $token, string $cardNumber, string $cvv, string $expMonth, string $expYear)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse retryPayment(string $paymentId, ?string $details = null)
 * @method static \Osama\AlQaseh\Responses\PaymentResponse revokePayment(string $paymentId, ?string $details = null)
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
