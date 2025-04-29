<?php

namespace Osama\AlQaseh\Tests\Unit;

use Orchestra\Testbench\TestCase;
use Osama\AlQaseh\AlQasehServiceProvider;
use Osama\AlQaseh\AlQaseh;
use InvalidArgumentException;
use Osama\AlQaseh\Responses\PaymentResponse;
use Illuminate\Support\Facades\Http;
use Mockery;

class AlQasehTest extends TestCase
{
    protected AlQaseh $alqaseh;
    protected $mockResponse;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create AlQaseh instance with test credentials
        $this->alqaseh = new AlQaseh(
            'test-api-key',
            'test-merchant-id',
            'https://api-test.alqaseh.com/v1',
            true
        );

        // Create mock response
        $this->mockResponse = Mockery::mock(PaymentResponse::class);

        // Fake all HTTP requests
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'data' => [
                    'payment_id' => 'test-payment-id',
                    'amount' => 100.00,
                    'currency' => 'USD',
                    'status' => 'succeeded'
                ]
            ], 200)
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [AlQasehServiceProvider::class];
    }

    /** @test */
    public function it_can_create_payment()
    {
        $response = $this->alqaseh->createPayment(
            100.00,
            'USD',
            'TEST123',
            'Test Payment',
            'https://example.com/redirect',
            'Retail',
            'US',
            ['custom' => 'data'],
            'test@example.com'
        );

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/egw/payments/create') &&
                   $request->method() === 'POST';
        });
    }

    /** @test */
    public function it_can_get_payment_info()
    {
        // Mock specific response for payment info
        Http::fake([
            'https://api-test.alqaseh.com/v1/egw/payments/info/test-token' => Http::response([
                'amount' => 100.00,
                'currency' => 'USD',
                'description' => 'Test Payment',
                'payment_status' => 'succeeded'
            ], 200)
        ]);

        $response = $this->alqaseh->getPaymentInfoByToken('test-payment-id');
        
       

        $this->assertNotNull($response);
    }

    /** @test */
    public function it_can_process_payment()
    {
        // Mock specific response for payment processing
        Http::fake([
            'https://api-test.alqaseh.com/v1/egw/payments/process/test-token' => Http::response('Payment processed successfully', 200)
        ]);

        $response = $this->alqaseh->processPayment(
            'test-token',
            '5341432908085972',
            '972',
            '11',
            '2026'
        );

      

        $this->assertNotNull($response);
    }

    /** @test */
    public function it_can_get_payment_history()
    {
        $response = $this->alqaseh->getPaymentHistory([
            'from' => '2024-01-01',
            'to' => '2024-12-31',
            'payment_status' => 'succeeded'
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/egw/payments/history') &&
                   $request->method() === 'GET';
        });
    }

    /** @test */
    public function it_can_download_payment_history()
    {
        $response = $this->alqaseh->downloadPaymentHistory([
            'from' => '2024-01-01',
            'to' => '2024-12-31'
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/egw/payments/history/download') &&
                   $request->method() === 'GET';
        });
    }

    /** @test */
    public function it_can_retry_payment()
    {
        $response = $this->alqaseh->retryPayment(
            'test-payment-id',
            'Retry attempt'
        );

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/egw/payments/retry') &&
                   $request->method() === 'POST';
        });
    }

    /** @test */
    public function it_can_revoke_payment()
    {
        $response = $this->alqaseh->revokePayment(
            'test-payment-id',
            'Revoke reason'
        );

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/egw/payments/revoke') &&
                   $request->method() === 'POST';
        });
    }

    /** @test */
    public function it_throws_exception_for_invalid_payment_amount()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->alqaseh->createPayment(
            -100.00,
            'USD',
            'TEST123',
            'Test Payment',
            'https://example.com/redirect'
        );
    }

    /** @test */
    public function it_throws_exception_for_invalid_payment_status_filter()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->alqaseh->getPaymentHistory([
            'payment_status' => 'invalid-status'
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}