<?php

namespace App\Http\Controllers;

use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function notification(Request $request, MidtransService $service)
    {
        try {
            $service->handleNotification($request->all());
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => 'OK']);
    }

    public function finish(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');
        if ($orderId !== '') {
            return redirect()->route('qmas.kasir')->with('success', 'Status pembayaran ' . $orderId . ' akan diperbarui otomatis.');
        }

        return redirect()->route('qmas.kasir')->with('success', 'Status pembayaran akan diperbarui otomatis.');
    }
}
