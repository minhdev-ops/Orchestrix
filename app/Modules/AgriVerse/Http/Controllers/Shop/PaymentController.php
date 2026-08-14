<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Transaction;
use App\Modules\AgriVerse\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show payment page for an order
     */
    public function index(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $existingTransaction = Transaction::where('order_id', $order->id)->first();

        if ($existingTransaction && $existingTransaction->payment_status === 'paid') {
            return redirect()->route('agriverse.shop.checkout.success', $order)
                ->with('success', 'Đơn hàng đã được thanh toán.');
        }

        $order->load(['product', 'store']);

        return Inertia::render('Marketplace/Payment/Index', [
            'order' => [
                'id' => $order->id,
                'total_amount' => $order->total_amount,
                'product' => [
                    'name' => optional($order->product)->name,
                    'price' => optional($order->product)->price,
                ],
                'store' => [
                    'name' => optional($order->store)->name,
                ],
            ],
            'paymentMethods' => $this->paymentService->getAvailableMethods(),
            'transaction' => $existingTransaction ? [
                'id' => $existingTransaction->transaction_id,
                'status' => $existingTransaction->payment_status,
            ] : null,
        ]);
    }

    /**
     * Process payment
     */
    public function process(Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_method' => 'required|string|in:cod,banking,vnpay,momo',
        ]);

        $paymentMethod = $request->payment_method;

        switch ($paymentMethod) {
            case 'vnpay':
                $returnUrl = route('agriverse.shop.payment.vnpay-callback', $order);
                $paymentUrl = $this->paymentService->createVnpayPayment($order, $returnUrl);

                if ($paymentUrl) {
                    return response()->json(['payment_url' => $paymentUrl]);
                }

                return back()->withErrors(['payment_method' => 'Không thể tạo thanh toán VNPay']);

            case 'momo':
                $returnUrl = route('agriverse.shop.payment.momo-callback', $order);
                $paymentUrl = $this->paymentService->createMomoPayment($order, $returnUrl);

                if ($paymentUrl) {
                    return response()->json(['payment_url' => $paymentUrl]);
                }

                return back()->withErrors(['payment_method' => 'Không thể tạo thanh toán MoMo']);

            case 'banking':
                $transaction = $this->paymentService->processBankingPayment($order);

                return redirect()->route('agriverse.shop.payment.banking', $order);

            case 'cod':
            default:
                $transaction = $this->paymentService->processCodPayment($order);

                return redirect()->route('agriverse.shop.checkout.success', $order)
                    ->with('success', 'Đặt hàng thành công! Thanh toán khi nhận hàng.');
        }
    }

    /**
     * VNPay callback (return from VNPay)
     */
    public function vnpayCallback(Request $request, Order $order)
    {
        $input = $request->all();
        $result = $this->paymentService->verifyVnpayReturn($input);

        $transaction = Transaction::where('order_id', $order->id)
            ->where('payment_method', 'vnpay')
            ->latest()
            ->first();

        if ($result['success'] && $result['response_code'] === '00') {
            if ($transaction) {
                $this->paymentService->markAsPaid($transaction, $result['transaction_no']);
            }

            return redirect()->route('agriverse.shop.checkout.success', $order)
                ->with('success', 'Thanh toán thành công qua VNPay!');
        }

        if ($transaction) {
            $this->paymentService->markAsFailed($transaction, $result['message'] ?? 'Payment failed');
        }

        return redirect()->route('agriverse.shop.payment.index', $order)
            ->withErrors(['payment' => $result['message'] ?? 'Thanh toán thất bại']);
    }

    /**
     * VNPay IPN (server-to-server)
     */
    public function vnpayIpn(Request $request, Order $order)
    {
        $input = $request->all();
        $result = $this->paymentService->verifyVnpayReturn($input);

        $transaction = Transaction::where('order_id', $order->id)
            ->where('payment_method', 'vnpay')
            ->latest()
            ->first();

        if ($result['success'] && $result['response_code'] === '00') {
            if ($transaction && $transaction->payment_status !== 'paid') {
                $this->paymentService->markAsPaid($transaction, $result['transaction_no']);
            }

            return response()->json(['RspCode' => '00', 'Message' => 'success']);
        }

        return response()->json(['RspCode' => '99', 'Message' => 'fail']);
    }

    /**
     * MoMo callback (return from MoMo)
     */
    public function momoCallback(Request $request, Order $order)
    {
        $input = $request->all();
        $result = $this->paymentService->verifyMomoReturn($input);

        $transaction = Transaction::where('order_id', $order->id)
            ->where('payment_method', 'momo')
            ->latest()
            ->first();

        if ($result['success'] && $result['response_code'] === 0) {
            if ($transaction) {
                $this->paymentService->markAsPaid($transaction, $result['transaction_no']);
            }

            return redirect()->route('agriverse.shop.checkout.success', $order)
                ->with('success', 'Thanh toán thành công qua MoMo!');
        }

        if ($transaction) {
            $this->paymentService->markAsFailed($transaction, $result['message'] ?? 'Payment failed');
        }

        return redirect()->route('agriverse.shop.payment.index', $order)
            ->withErrors(['payment' => $result['message'] ?? 'Thanh toán thất bại']);
    }

    /**
     * MoMo IPN (server-to-server)
     */
    public function momoIpn(Request $request, Order $order)
    {
        $input = $request->all();
        $result = $this->paymentService->verifyMomoReturn($input);

        $transaction = Transaction::where('order_id', $order->id)
            ->where('payment_method', 'momo')
            ->latest()
            ->first();

        if ($result['success'] && $result['response_code'] === 0) {
            if ($transaction && $transaction->payment_status !== 'paid') {
                $this->paymentService->markAsPaid($transaction, $result['transaction_no']);
            }

            return response()->json(['resultCode' => 0, 'message' => 'success']);
        }

        return response()->json(['resultCode' => 1, 'message' => 'fail']);
    }

    /**
     * Show banking payment info
     */
    public function banking(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['product', 'store']);

        return Inertia::render('Marketplace/Payment/Banking', [
            'order' => [
                'id' => $order->id,
                'total_amount' => $order->total_amount,
                'product' => [
                    'name' => optional($order->product)->name,
                ],
            ],
            'bankInfo' => [
                'bank_name' => config('app.bank_name', 'Vietcombank'),
                'account_number' => config('app.bank_account', '0123456789'),
                'account_name' => config('app.bank_holder', 'CONG TY AGRIVERSE'),
                'branch' => config('app.bank_branch', 'Ha Noi'),
                'content' => "AGRI{$order->id}",
            ],
        ]);
    }

    /**
     * Upload banking proof
     */
    public function uploadProof(Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'proof' => 'required|image|max:5120',
        ]);

        $transaction = Transaction::where('order_id', $order->id)
            ->where('payment_method', 'banking')
            ->latest()
            ->first();

        if ($transaction) {
            $path = $request->file('proof')->store('payment-proofs', 'public');
            $transaction->update(['payment_proof' => $path]);
        }

        return back()->with('success', 'Đã tải lên bằng chứng chuyển khoản. Vui lòng chờ xác nhận.');
    }
}
