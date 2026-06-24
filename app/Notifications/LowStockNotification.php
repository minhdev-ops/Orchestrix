<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Modules\AgriVerse\Models\Product;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Cảnh báo tồn kho thấp - {$this->product->name}")
            ->line("Sản phẩm **{$this->product->name}** chỉ còn **{$this->product->stock}** sản phẩm trong kho.")
            ->line("Vui lòng bổ sung tồn kho để tránh mất doanh thu.")
            ->action('Xem sản phẩm', route('agriverse.shop.products.show', $this->product->id))
            ->line('Cảm ơn bạn đã sử dụng AgriVerse!');
    }

    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->stock,
            'message' => "Sản phẩm {$this->product->name} chỉ còn {$this->product->stock} sản phẩm",
        ];
    }
}
