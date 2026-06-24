<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Transaction;

class PaymentService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('payment');
    }

    /**
     * Create VNPay payment URL
     */
    public function createVnpayPayment(Order $order, string $returnUrl): string
    {
        $vnpayConfig = $this->config['vnpay'];

        $vnp_TmnCode = $vnpayConfig['tmn_code'];
        $vnp_HashSecret = $vnpayConfig['hash_secret'];
        $vnp_Url = $vnpayConfig['payment_url'];
        $vnp_Returnurl = $returnUrl;

        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = "Thanh toan don hang #{$order->id}";
        $vnp_OrderType = 'other';
        $vnp_Amount = $order->total_amount * 100; // VNPay uses amount x 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = request()->ip();
        $vnp_CreateDate = now()->format('YmdHis');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Locale' => $vnp_Locale,
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $vnp_TxnRef,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_Amount' => $vnp_Amount,
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_CreateDate' => $vnp_CreateDate,
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $querystring = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i) {
                $querystring .= '&' . $key . '=' . $value;
            } else {
                $querystring .= $key . '=' . $value;
                $i++;
            }
        }

        $vnp_SecureHash = hash_hmac('sha512', $querystring, $vnp_HashSecret);
        $vnp_Url .= '?' . $querystring . '&vnp_SecureHash=' . $vnp_SecureHash;

        // Create pending transaction
        $this->createTransaction($order, 'vnpay', 'pending');

        return $vnp_Url;
    }

    /**
     * Verify VNPay callback/IPN
     */
    public function verifyVnpayReturn(array $input): array
    {
        $vnpayConfig = $this->config['vnpay'];
        $vnp_HashSecret = $vnpayConfig['hash_secret'];

        $vnp_SecureHash = $input['vnp_SecureHash'] ?? '';
        unset($input['vnp_SecureHash'], $input['vnp_SecureHashType']);

        ksort($input);
        $querystring = '';
        $i = 0;
        foreach ($input as $key => $value) {
            if ($i) {
                $querystring .= '&' . $key . '=' . $value;
            } else {
                $querystring .= $key . '=' . $value;
                $i++;
            }
        }

        $checkHash = hash_hmac('sha512', $querystring, $vnp_HashSecret);

        if ($checkHash === $vnp_SecureHash) {
            return [
                'success' => true,
                'order_id' => $input['vnp_TxnRef'],
                'transaction_no' => $input['vnp_TransactionNo'] ?? null,
                'amount' => ($input['vnp_Amount'] ?? 0) / 100,
                'response_code' => $input['vnp_ResponseCode'] ?? null,
                'message' => $this->getVnpayResponseMessage($input['vnp_ResponseCode'] ?? ''),
            ];
        }

        return ['success' => false, 'message' => 'Invalid signature'];
    }

    /**
     * Create MoMo payment
     */
    public function createMomoPayment(Order $order, string $returnUrl): ?string
    {
        $momoConfig = $this->config['momo'];
        $partnerCode = $momoConfig['partner_code'];
        $accessKey = $momoConfig['access_key'];
        $secretKey = $momoConfig['secret_key'];
        $endpoint = $momoConfig['endpoint'];

        $orderId = $order->id;
        $orderInfo = "Thanh toan don hang #{$orderId}";
        $amount = $order->total_amount;
        $requestId = $orderId . '_' . time();
        $extraData = '';

        // Create HMAC SHA256 signature
        $rawSignature = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$momoConfig['ipn_url']}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$returnUrl}&requestId={$requestId}&requestType=payWithMethod";
        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        $requestData = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'AgriVerse',
            'storeId' => 'AgriVerse',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl' => $momoConfig['ipn_url'],
            'lang' => 'vi',
            'requestType' => 'payWithMethod',
            'autoCapture' => true,
            'extraData' => $extraData,
            'signature' => $signature,
        ];

        try {
            $response = Http::post("{$endpoint}/create", $requestData);

            if ($response->successful() && $response->json('resultCode') === 0) {
                $this->createTransaction($order, 'momo', 'pending');
                return $response->json('payUrl');
            }

            Log::error('MoMo payment creation failed', $response->json());
            return null;
        } catch (\Exception $e) {
            Log::error('MoMo payment error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Verify MoMo callback/IPN
     */
    public function verifyMomoReturn(array $input): array
    {
        $momoConfig = $this->config['momo'];
        $accessKey = $momoConfig['access_key'];
        $secretKey = $momoConfig['secret_key'];

        $rawSignature = "accessKey={$accessKey}&amount={$input['amount']}&extraData={$input['extraData']}&message={$input['message']}&orderId={$input['orderId']}&orderInfo={$input['orderInfo']}&orderType={$input['orderType']}&partnerCode={$input['partnerCode']}&payType={$input['payType']}&requestId={$input['requestId']}&responseTime={$input['responseTime']}&resultCode={$input['resultCode']}&transId={$input['transId']}";
        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        if ($signature === $input['signature']) {
            return [
                'success' => true,
                'order_id' => $input['orderId'],
                'transaction_no' => $input['transId'] ?? null,
                'amount' => $input['amount'],
                'response_code' => $input['resultCode'],
                'message' => $input['message'] ?? '',
            ];
        }

        return ['success' => false, 'message' => 'Invalid signature'];
    }

    /**
     * Process COD payment
     */
    public function processCodPayment(Order $order): Transaction
    {
        return $this->createTransaction($order, 'cod', 'pending');
    }

    /**
     * Process Banking transfer
     */
    public function processBankingPayment(Order $order, ?string $proof = null): Transaction
    {
        return $this->createTransaction($order, 'banking', 'pending', $proof);
    }

    /**
     * Mark transaction as paid
     */
    public function markAsPaid(Transaction $transaction, ?string $gatewayTransactionId = null): Transaction
    {
        $transaction->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'notes' => $gatewayTransactionId ? "Gateway TX: {$gatewayTransactionId}" : $transaction->notes,
        ]);

        // Update order status
        $order = $transaction->order;
        if ($order && $order->status === 'pending') {
            $order->update(['status' => 'confirmed']);
            \App\Modules\AgriVerse\Models\OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'confirmed',
                'note' => 'Payment received via ' . $transaction->payment_method,
            ]);
        }

        return $transaction->fresh();
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(Transaction $transaction, ?string $reason = null): Transaction
    {
        $transaction->update([
            'payment_status' => 'failed',
            'notes' => $reason,
        ]);

        return $transaction->fresh();
    }

    /**
     * Create a transaction record
     */
    protected function createTransaction(
        Order $order,
        string $method,
        string $status = 'pending',
        ?string $proof = null
    ): Transaction {
        $commissionRate = $this->config['commission_rate'] ?? 5;
        $commissionFee = round($order->total_amount * ($commissionRate / 100), 2);

        return Transaction::create([
            'transaction_id' => $this->generateTransactionId($order, $method),
            'order_id' => $order->id,
            'user_id' => $order->buyer_id,
            'amount' => $order->total_amount,
            'commission_fee' => $commissionFee,
            'seller_amount' => $order->total_amount - $commissionFee,
            'payment_method' => $method,
            'payment_status' => $status,
            'payment_proof' => $proof,
            'notes' => null,
        ]);
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId(Order $order, string $method): string
    {
        $prefix = strtoupper(substr($method, 0, 3));
        return "{$prefix}{$order->id}" . strtoupper(uniqid());
    }

    /**
     * Get VNPay response message
     */
    protected function getVnpayResponseMessage(string $code): string
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Xác thực không đúng quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán',
            '12' => 'Thẻ/Tài khoản bị khóa',
            '13' => 'Nhập sai mật khẩu xác thực (OTP)',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng đang bảo trì',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Lỗi không xác định',
        ];

        return $messages[$code] ?? "Lỗi: {$code}";
    }

    /**
     * Get available payment methods
     */
    public function getAvailableMethods(): array
    {
        $methods = [
            'cod' => [
                'id' => 'cod',
                'name' => 'Thanh toán khi nhận hàng (COD)',
                'description' => 'Thanh toán tiền mặt khi nhận hàng',
                'icon' => 'local_shipping',
                'enabled' => true,
            ],
            'banking' => [
                'id' => 'banking',
                'name' => 'Chuyển khoản ngân hàng',
                'description' => 'Chuyển khoản trực tiếp đến tài khoản ngân hàng',
                'icon' => 'account_balance',
                'enabled' => true,
            ],
        ];

        if ($this->config['vnpay']['enabled']) {
            $methods['vnpay'] = [
                'id' => 'vnpay',
                'name' => 'VNPay',
                'description' => 'Thanh toán qua VNPay (ATM, Visa, MasterCard)',
                'icon' => 'credit_card',
                'enabled' => true,
            ];
        }

        if ($this->config['momo']['enabled']) {
            $methods['momo'] = [
                'id' => 'momo',
                'name' => 'Ví MoMo',
                'description' => 'Thanh toán qua ví điện tử MoMo',
                'icon' => 'account_balance_wallet',
                'enabled' => true,
            ];
        }

        return $methods;
    }
}
