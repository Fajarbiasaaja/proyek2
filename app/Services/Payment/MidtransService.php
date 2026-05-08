<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MidtransService - Midtrans Payment Gateway Integration
 * 
 * Service untuk menghandle semua API calls ke Midtrans
 * Support: Snap API untuk redirect ke Midtrans payment page
 * 
 * Fitur:
 * - Create transaction di Midtrans
 * - Check transaction status
 * - Get Snap token untuk payment
 */
class MidtransService
{
    /**
     * Midtrans configuration
     */
    private string $serverKey;
    private string $clientKey;
    private string $merchantId;
    private bool $isProduction;
    private string $apiUrl;

    /**
     * Constructor - Load Midtrans config
     */
    public function __construct()
    {
        $config = Config::get('services.midtrans');

        $this->serverKey = $config['server_key'] ?? '';
        $this->clientKey = $config['client_key'] ?? '';
        $this->merchantId = $config['merchant_id'] ?? '';
        $this->isProduction = $config['is_production'] ?? false;
        $this->apiUrl = $config['api_url'] ?? 'https://app.sandbox.midtrans.com';
    }

    /**
     * Create transaction di Midtrans
     * 
     * @param array $data Transaction data
     * @return array
     */
    public function createTransaction(array $data): array
    {
        try {
            // Validasi required fields
            if (empty($data['order_id']) || empty($data['gross_amount'])) {
                return [
                    'success' => false,
                    'error' => 'order_id dan gross_amount wajib',
                ];
            }

            // Build transaction request untuk Midtrans
            $transactionData = [
                'transaction_details' => [
                    'order_id' => $data['order_id'],
                    'gross_amount' => $data['gross_amount'],
                ],
                'customer_details' => [
                    'email' => $data['customer_email'] ?? '',
                    'first_name' => $data['customer_name'] ?? '',
                ],
                'item_details' => [
                    [
                        'id' => 'INVOICE-' . $data['invoice_id'],
                        'price' => $data['gross_amount'],
                        'quantity' => 1,
                        'name' => 'Invoice Payment #' . $data['invoice_id'],
                    ],
                ],
            ];

            // Add custom data
            if (!empty($data['payment_id'])) {
                $transactionData['custom_field1'] = 'payment_id:' . $data['payment_id'];
            }

            // Call Midtrans API untuk create transaction
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->apiUrl . '/v1/transactions', $transactionData);

            $responseData = $response->json();

            // Check response status
            if ($response->successful() && isset($responseData['token'])) {
                return [
                    'success' => true,
                    'transaction_id' => $responseData['transaction_id'] ?? null,
                    'payment_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $responseData['token'],
                    'snap_token' => $responseData['token'],
                    'response' => $responseData,
                ];
            }

            // Jika gagal
            Log::error('Midtrans transaction creation failed', [
                'request' => $transactionData,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'error' => $responseData['status_message'] ?? 'Failed to create transaction',
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans API error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check transaction status
     * 
     * @param string $transactionId
     * @return array
     */
    public function checkTransaction(string $transactionId): array
    {
        try {
            // Call Midtrans API untuk get transaction status
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->apiUrl . '/v1/transactions/' . $transactionId);

            $responseData = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'transaction_id' => $responseData['transaction_id'] ?? null,
                    'status' => $responseData['transaction_status'] ?? 'unknown',
                    'response' => $responseData,
                ];
            }

            return [
                'success' => false,
                'error' => $responseData['status_message'] ?? 'Failed to check transaction',
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans check transaction error', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Approve transaction (manual)
     * 
     * @param string $transactionId
     * @return array
     */
    public function approveTransaction(string $transactionId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->apiUrl . '/v1/transactions/' . $transactionId . '/approve');

            $responseData = $response->json();

            return [
                'success' => $response->successful(),
                'response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans approve transaction error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Deny transaction
     * 
     * @param string $transactionId
     * @return array
     */
    public function denyTransaction(string $transactionId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->apiUrl . '/v1/transactions/' . $transactionId . '/deny');

            $responseData = $response->json();

            return [
                'success' => $response->successful(),
                'response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans deny transaction error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Refund transaction
     * 
     * @param string $transactionId
     * @param int|null $refundAmount Optional: partial refund amount
     * @return array
     */
    public function refundTransaction(string $transactionId, ?int $refundAmount = null): array
    {
        try {
            $data = [];
            if ($refundAmount) {
                $data['refund_amount'] = $refundAmount;
            }

            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->apiUrl . '/v1/transactions/' . $transactionId . '/refund', $data);

            $responseData = $response->json();

            return [
                'success' => $response->successful(),
                'response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans refund transaction error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature (security)
     * 
     * @param array $data Webhook data
     * @param string $signature Signature dari Midtrans
     * @return bool
     */
    public function verifySignature(array $data, string $signature): bool
    {
        try {
            // Prepare data untuk verification
            $orderId = $data['order_id'] ?? '';
            $statusCode = $data['status_code'] ?? '';
            $grossAmount = $data['gross_amount'] ?? '';

            // Create signature
            $signInput = $orderId . $statusCode . $grossAmount . $this->serverKey;
            $hash = hash('sha512', $signInput);

            // Compare dengan provided signature
            return hash_equals($hash, $signature);
        } catch (\Exception $e) {
            Log::error('Midtrans signature verification error', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get client key untuk frontend
     * 
     * @return string
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Check apakah Midtrans properly configured
     * 
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->serverKey) 
            && !empty($this->clientKey) 
            && !empty($this->merchantId);
    }
}
