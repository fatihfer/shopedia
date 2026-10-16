<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans) {}

    public function handle(Request $request): JsonResponse
    {
        $data = $request->all();

        if (! $this->midtrans->isValidSignature($data)) {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $order = Order::where('gateway_ref', $data['order_id'] ?? null)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found.']);
        }

        $this->midtrans->applyStatus(
            $order,
            $data['transaction_status'] ?? '',
            $data['fraud_status'] ?? ''
        );

        return response()->json(['message' => 'OK']);
    }
}
