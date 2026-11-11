<?php

namespace App\Services\Payment;

use App\Models\AkunKas;
use App\Models\Payment;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\TransaksiKas;
use Illuminate\Support\Facades\DB;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $this->configure();
    }

    protected function configure(): void
    {
        MidtransConfig::$serverKey = (string) config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
    }

    public function createPayment(Penjualan $penjualan): array
    {
        $orderId = 'QMAS-' . $penjualan->id . '-' . now()->format('YmdHis');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) round((float) $penjualan->total_harga),
            ],
            'customer_details' => [
                'first_name' => $penjualan->pelanggan ?: 'Umum',
            ],
            'item_details' => [
                ['id' => 'QMAS-ITEM', 'price' => (int) round((float) $penjualan->total_harga), 'quantity' => 1, 'name' => 'Transaksi Kasir QMAS'],
            ],
            'callbacks' => [
                'finish' => rtrim((string) config('app.url'), '/') . '/midtrans/finish',
            ],
        ];

        $transaction = Snap::createTransaction($params);

        Payment::create([
            'penjualan_id' => $penjualan->id,
            'provider' => 'midtrans',
            'order_id' => $orderId,
            'status' => 'pending',
            'gross_amount' => $penjualan->total_harga,
            'payload' => $params,
        ]);

        $penjualan->update([
            'payment_provider' => 'midtrans',
            'payment_reference' => $orderId,
            'payment_url' => $transaction->redirect_url ?? null,
        ]);

        return [
            'token' => $transaction->token ?? null,
            'redirect_url' => $transaction->redirect_url ?? null,
            'order_id' => $orderId,
        ];
    }

    public function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $serverKey = (string) config('services.midtrans.server_key');

        if ($orderId === '' || $signatureKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signatureKey);
    }

    public function handleNotification(array $payload): void
    {
        if (! $this->verifySignature($payload)) {
            throw new \RuntimeException('Invalid Midtrans signature.');
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $payment = Payment::where('order_id', $orderId)->firstOrFail();
        $penjualan = $payment->penjualan;

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        $payment->update([
            'status' => $transactionStatus ?: $payment->status,
            'fraud_status' => $fraudStatus,
            'payment_type' => $paymentType,
            'payload' => $payload,
            'transaction_time' => isset($payload['transaction_time']) ? \Carbon\Carbon::parse($payload['transaction_time']) : now(),
        ]);

        if (in_array($transactionStatus, ['capture', 'settlement'], true) && ($fraudStatus === null || $fraudStatus === 'accept')) {
            DB::transaction(function () use ($penjualan, $payment) {
                $locked = Penjualan::lockForUpdate()->find($penjualan->id);
                if (! $locked || $locked->status_pembayaran === 'Lunas') {
                    return;
                }

                $locked->update([
                    'status_pembayaran' => 'Lunas',
                    'paid_at' => now(),
                ]);

                foreach (explode(',', (string) $locked->item_pesanan) as $part) {
                    if (preg_match('/^(.+?)\s*x(\d+)\s*$/i', trim($part), $m)) {
                        $produk = Produk::where('nama_produk', trim($m[1]))->first();
                        if ($produk) {
                            $produk->decrement('stok', (int) $m[2]);
                        }
                    }
                }

                $akun = AkunKas::orderBy('nama_kas')->first();
                if ($akun) {
                    $akun->increment('saldo_saat_ini', (float) $locked->total_harga);
                    TransaksiKas::create([
                        'jenis_transaksi' => 'Masuk',
                        'akun_sumber_id' => $akun->id,
                        'nominal' => $locked->total_harga,
                        'keterangan' => 'Pembayaran Midtrans ' . $payment->order_id,
                        'tanggal' => now()->toDateString(),
                    ]);
                }
            });

            return;
        }

        if (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
            $penjualan->update(['status_pembayaran' => 'Gagal']);
        }
    }
}
