<?php
namespace App\Modules\AgriVerse\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GardenNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $message,
        public ?string $plantId = null,
        public ?string $gardenId = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'plant_id' => $this->plantId,
            'garden_id' => $this->gardenId,
            'time' => now()->toISOString(),
        ];
    }
}
