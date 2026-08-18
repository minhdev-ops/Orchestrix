<?php

namespace App\Console\Commands;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredOfferOrders extends Command
{
    protected $signature = 'offers:cancel-expired {--dry : Chỉ liệt kê, không cập nhật}';

    protected $description = 'Hủy đơn hàng từ đề xuất giá khi người mua không xác nhận trong 12h';

    public function handle(): int
    {
        $expired = Order::where('status', 'pending_confirmation')
            ->where('confirm_deadline', '<', Carbon::now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Không có đơn nào hết hạn xác nhận.');

            return self::SUCCESS;
        }

        foreach ($expired as $order) {
            if ($this->option('dry')) {
                $this->line("[dry] Order #{$order->id} (offer: {$order->offer_id}) sẽ bị hủy");
                continue;
            }

            $order->update([
                'status' => 'cancelled',
                'cancel_reason' => 'Người mua không xác nhận đơn hàng trong vòng 12h.',
                'cancelled_at' => Carbon::now(),
            ]);

            OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'note' => 'Tự động hủy: người mua không xác nhận trong 12h.',
                'user_id' => $order->buyer_id,
            ]);

            $this->info("Đã hủy Order #{$order->id} (offer: {$order->offer_id})");
        }

        return self::SUCCESS;
    }
}
