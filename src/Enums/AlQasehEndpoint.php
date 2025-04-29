<?php

namespace Osama\AlQaseh\Enums;

enum AlQasehEndpoint: string
{
    case CREATE_PAYMENT = '/egw/payments/create';
    case PAYMENT_HISTORY = '/egw/payments/history';
    case DOWNLOAD_PAYMENT_HISTORY = '/egw/payments/history/download';
    case PAYMENT_INFO_BY_TOKEN = '/egw/payments/info/{token}';
    case PROCESS_PAYMENT = '/egw/payments/process/{token}';
    case PAYMENT_STATUS = '/payments/{transactionId}/status';
    case RETRY_PAYMENT = '/egw/payments/retry';
    case REVOKE_PAYMENT = '/egw/payments/revoke';
    case PAYMENT_DETAILS = '/egw/payments/{id}';

    /**
     * Get the endpoint with parameters replaced
     *
     * @param array $params
     * @return string
     */
    public function getEndpoint(array $params = []): string
    {
        $endpoint = $this->value;
        foreach ($params as $key => $value) {
            $endpoint = str_replace('{' . $key . '}', $value, $endpoint);
        }
        return $endpoint;
    }
}