<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Config;

/**
 * PaymentGatewayService - Payment Gateway Manager
 * 
 * Service untuk menghandle payment gateway logic
 * Support untuk multiple gateways (Midtrans, manual, etc)
 * 
 * Fungsi:
 * 1. Create payment transaction di gateway
 * 2. Verifikasi payment status dari gateway
 * 3. Handle webhook dari gateway
 * 4. Update invoice status setelah payment sukses
 */
class PaymentGatewayService
{
    /**
     * Create payment di payment gateway
     * 
     * @param Invoice $invoice
     * @param float $amount
     * @param string $paymentMethod
     * @return array Berisi: payment_url, transaction_id, payment_id
     */
    public function createPayment(Invoice $invoice, float $amount, string $paymentMethod): array
    {
        // Tentukan gateway berdasarkan payment method
        $gateway = $this->determineGateway($paymentMethod);
        
        // Create payment record di database
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_gateway' => $gateway,
            'status' => 'pending_approval',
            'submitted_date' => now(),
        ]);

        // Jika gateway adalah Midtrans, create transaction di Midtrans
        if ($gateway === 'midtrans') {
            return $this->createMidtransPayment($payment, $invoice);
        }

        // Untuk manual gateway, return payment data biasa
        return [
            'success' => true,
            'payment_id' => $payment->id,
            'payment_url' => null,
            'transaction_id' => null,
            'message' => 'Payment berhasil dibuat. Silakan lanjutkan pembayaran.',
        ];
    }

    /**
     * Create payment di Midtrans
     * 
     * @param Payment $payment
     * @param Invoice $invoice
     * @return array
     */
    private function createMidtransPayment(Payment $payment, Invoice $invoice): array
    {
        $midtransService = new MidtransService();
        
        $result = $midtransService->createTransaction([
            'order_id' => 'INV-' . $invoice->id . '-' . $payment->id,
            'gross_amount' => (int) $payment->amount,
            'payment_method' => $payment->payment_method,
            'invoice_id' => $invoice->id,
            'payment_id' => $payment->id,
            'customer_email' => $invoice->booking->customer->email,
            'customer_name' => $invoice->booking->customer->name,
        ]);

        if ($result['success']) {
            // Update payment dengan gateway data
            $payment->update([
                'transaction_id' => $result['transaction_id'],
                'payment_url' => $result['payment_url'],
                'gateway_response' => $result['response'],
                'gateway_status' => 'pending',
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'payment_url' => $result['payment_url'],
                'transaction_id' => $result['transaction_id'],
                'message' => 'Payment berhasil dibuat. Silakan lanjutkan pembayaran.',
            ];
        }

        return [
            'success' => false,
            'message' => $result['error'] ?? 'Gagal membuat payment di gateway',
        ];
    }

    /**
     * Verify payment status dari gateway
     * 
     * @param Payment $payment
     * @return array
     */
    public function verifyPayment(Payment $payment): array
    {
        if ($payment->payment_gateway === 'midtrans') {
            $midtransService = new MidtransService();
            return $midtransService->checkTransaction($payment->transaction_id);
        }

        return [
            'success' => false,
            'message' => 'Gateway tidak dikenal',
        ];
    }

    /**
     * Approve payment & update invoice
     * 
     * @param Payment $payment
     * @param string|null $notes
     * @return bool
     */
    public function approvePayment(Payment $payment, ?string $notes = null): bool
    {
        // Update payment status
        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_date' => now(),
            'notes' => $notes ?? $payment->notes,
        ]);

        // Update invoice status ke paid
        $invoice = $payment->invoice;
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return true;
    }

    /**
     * Handle webhook callback dari payment gateway
     * 
     * @param array $webhookData
     * @return bool
     */
    public function handleWebhook(array $webhookData): bool
    {
        // Cari payment berdasarkan transaction ID
        $payment = Payment::where('transaction_id', $webhookData['transaction_id'])->first();

        if (!$payment) {
            return false;
        }

        // Update gateway status
        $payment->update([
            'gateway_status' => $webhookData['status'],
            'gateway_response' => $webhookData['response'],
        ]);

        // Jika payment settlement, auto-approve
        if ($webhookData['status'] === 'settlement') {
            $this->approvePayment($payment, 'Otomatis approved dari ' . $payment->payment_gateway);
        }

        // Jika payment failed
        if (in_array($webhookData['status'], ['failure', 'deny', 'cancel'])) {
            $payment->update([
                'status' => 'rejected',
                'notes' => 'Payment gagal di gateway: ' . ($webhookData['reason'] ?? 'Unknown'),
            ]);
        }

        return true;
    }

    /**
     * Determine gateway dari payment method
     * 
     * @param string $paymentMethod
     * @return string
     */
    private function determineGateway(string $paymentMethod): string
    {
        // E-wallet methods menggunakan Midtrans
        $midtransMethods = ['e_wallet', 'credit_card', 'bank_transfer'];

        if (in_array($paymentMethod, $midtransMethods) && Config::get('services.midtrans.enabled')) {
            return 'midtrans';
        }

        // Default ke manual
        return 'manual';
    }

    /**
     * Check apakah Midtrans enabled
     * 
     * @return bool
     */
    public static function isMidtransEnabled(): bool
    {
        return Config::get('services.midtrans.enabled', false);
    }

    /**
     * Get payment gateway info
     * 
     * @return array
     */
    public static function getGatewayInfo(): array
    {
        return [
            'midtrans_enabled' => self::isMidtransEnabled(),
            'supported_methods' => [
                'cash' => 'Manual (Tunai)',
                'bank_transfer' => 'Transfer Bank',
                'e_wallet' => 'E-Wallet (Midtrans)',
                'credit_card' => 'Kartu Kredit (Midtrans)',
                'check' => 'Cek',
            ],
        ];
    }
}
