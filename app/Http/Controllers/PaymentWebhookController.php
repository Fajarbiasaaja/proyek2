<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payment\PaymentGatewayService;
use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PaymentWebhookController - Payment Gateway Webhook Handler
 * 
 * Handle webhook callbacks dari payment gateway (Midtrans)
 * Midtrans akan mengirim status update untuk setiap payment
 * 
 * Webhook types:
 * - payment.success: Payment berhasil
 * - payment.pending: Payment pending
 * - payment.expired: Payment expired
 * - payment.failure: Payment gagal
 */
class PaymentWebhookController extends Controller
{
    /**
     * Handle Midtrans webhook notification
     * 
     * POST /api/webhooks/midtrans
     * 
     * Midtrans akan POST webhook ke endpoint ini untuk notify status update
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function midtransNotification(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('Midtrans Webhook Received', [
                'order_id' => $data['order_id'] ?? null,
                'status' => $data['transaction_status'] ?? null,
            ]);

            // Verifikasi signature
            $midtransService = new MidtransService();
            $isValid = $midtransService->verifySignature(
                $data,
                $data['signature_key'] ?? ''
            );

            if (!$isValid) {
                Log::warning('Invalid Midtrans webhook signature', $data);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Cari payment berdasarkan transaction_id
            $transactionId = $data['transaction_id'] ?? null;
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                Log::warning('Payment not found for transaction', [
                    'transaction_id' => $transactionId,
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Process webhook notification
            $this->processNotification($payment, $data);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans webhook processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Process Midtrans notification
     * 
     * @param Payment $payment
     * @param array $data Webhook data dari Midtrans
     * @return void
     */
    private function processNotification(Payment $payment, array $data): void
    {
        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus = $data['fraud_status'] ?? 'accept';

        // Map Midtrans status ke aplikasi
        $mappedStatus = $this->mapMidtransStatus($transactionStatus, $fraudStatus);

        // Update payment
        $payment->update([
            'gateway_status' => $transactionStatus,
            'gateway_response' => json_encode($data),
        ]);

        // Handle based on status
        switch ($mappedStatus) {
            case 'settlement':
            case 'capture':
                // Payment sukses
                $this->handlePaymentSuccess($payment, $data);
                break;

            case 'pending':
                // Payment masih pending
                $this->handlePaymentPending($payment, $data);
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                // Payment gagal
                $this->handlePaymentFailed($payment, $data, $mappedStatus);
                break;

            case 'challenge':
                // Payment dalam review/challenge
                $this->handlePaymentChallenge($payment, $data);
                break;
        }
    }

    /**
     * Handle payment success
     * 
     * @param Payment $payment
     * @param array $data
     * @return void
     */
    private function handlePaymentSuccess(Payment $payment, array $data): void
    {
        // Jika belum approved, auto-approve
        if ($payment->status !== 'approved') {
            $gatewayService = new PaymentGatewayService();
            $gatewayService->approvePayment(
                $payment,
                'Otomatis approved dari Midtrans (Status: ' . $data['transaction_status'] . ')'
            );

            Log::info('Payment auto-approved from Midtrans', [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
            ]);
        }
    }

    /**
     * Handle payment pending
     * 
     * @param Payment $payment
     * @param array $data
     * @return void
     */
    private function handlePaymentPending(Payment $payment, array $data): void
    {
        // Update notes
        $payment->update([
            'notes' => 'Payment pending di gateway (Midtrans)',
        ]);

        Log::info('Payment pending from Midtrans', [
            'payment_id' => $payment->id,
        ]);
    }

    /**
     * Handle payment failed
     * 
     * @param Payment $payment
     * @param array $data
     * @param string $reason
     * @return void
     */
    private function handlePaymentFailed(Payment $payment, array $data, string $reason): void
    {
        // Reject payment
        $payment->update([
            'status' => 'rejected',
            'notes' => 'Payment gagal di Midtrans (Reason: ' . $reason . ')',
        ]);

        Log::warning('Payment rejected from Midtrans', [
            'payment_id' => $payment->id,
            'reason' => $reason,
        ]);
    }

    /**
     * Handle payment challenge
     * 
     * @param Payment $payment
     * @param array $data
     * @return void
     */
    private function handlePaymentChallenge(Payment $payment, array $data): void
    {
        // Mark as pending review
        $payment->update([
            'notes' => 'Payment dalam review/challenge di Midtrans',
        ]);

        Log::info('Payment challenged from Midtrans', [
            'payment_id' => $payment->id,
        ]);
    }

    /**
     * Map Midtrans status ke internal status
     * 
     * Midtrans transaction status:
     * - settlement: Transaksi berhasil
     * - capture: Capture berhasil (untuk auth only)
     * - pending: Pending
     * - deny: Transaksi ditolak
     * - expire: Transaksi expired
     * - cancel: Transaksi dibatalkan
     * - challenge: Transaksi dalam challenge/review
     * 
     * @param string|null $transactionStatus
     * @param string|null $fraudStatus
     * @return string
     */
    private function mapMidtransStatus(?string $transactionStatus, ?string $fraudStatus): string
    {
        // Jika fraud detected, return denied
        if ($fraudStatus === 'deny') {
            return 'deny';
        }

        // Map transaction status
        return match($transactionStatus) {
            'settlement' => 'settlement',
            'capture' => 'capture',
            'pending' => 'pending',
            'deny' => 'deny',
            'expire' => 'expire',
            'cancel' => 'cancel',
            'challenge' => 'challenge',
            default => 'unknown',
        };
    }

    /**
     * Get payment status (untuk frontend polling/checking)
     * 
     * GET /api/payments/{id}/status
     * 
     * @param Payment $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentStatus(Payment $payment)
    {
        // Auth check - customer hanya bisa check own payment
        $user = auth()->user();
        if ($payment->invoice->booking->customer->email !== $user->email) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $payment->id,
            'status' => $payment->status,
            'gateway_status' => $payment->gateway_status,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'is_approved' => $payment->isApproved(),
            'is_pending' => $payment->isPending(),
            'is_rejected' => $payment->isRejected(),
            'is_gateway_pending' => $payment->isGatewayPending(),
            'is_gateway_settled' => $payment->isGatewaySettled(),
            'payment_url' => $payment->payment_url,
        ]);
    }
}
