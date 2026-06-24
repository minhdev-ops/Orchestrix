<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\EventlogBatch;

class AuditLogService
{
    protected string $batchName = 'default';

    /**
     * Start a new batch
     */
    public function startBatch(string $name = 'default'): self
    {
        $this->batchName = $name;
        activity()->batchName($name)->performedOn(auth()->user());
        return $this;
    }

    /**
     * Log an activity
     */
    public function log(string $description, array $properties = []): self
    {
        activity()
            ->tap(function ($event) use ($properties) {
                $event->properties = array_merge($event->properties ?? [], $properties);
                $event->ip_address = request()->ip();
                $event->user_agent = request()->userAgent();
            })
            ->log($description);

        return $this;
    }

    /**
     * Log CRUD operations
     */
    public function logCreated($model, array $extra = []): self
    {
        $className = class_basename($model);
        return $this->log("Created {$className}", array_merge([
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'attributes' => $model->getAttributes(),
        ], $extra));
    }

    public function logUpdated($model, array $old = [], array $new = [], array $extra = []): self
    {
        $className = class_basename($model);
        return $this->log("Updated {$className}", array_merge([
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old' => $old,
            'attributes' => $new,
        ], $extra));
    }

    public function logDeleted($model, array $extra = []): self
    {
        $className = class_basename($model);
        return $this->log("Deleted {$className}", array_merge([
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'attributes' => $model->getAttributes(),
        ], $extra));
    }

    /**
     * Log authentication events
     */
    public function logLogin($user): self
    {
        return $this->log("User {$user->email} logged in", [
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);
    }

    public function logLogout($user): self
    {
        return $this->log("User {$user->email} logged out", [
            'user_id' => $user->id,
        ]);
    }

    public function logFailedLogin(string $email, string $reason = ''): self
    {
        return $this->log("Failed login attempt for {$email}", [
            'email' => $email,
            'reason' => $reason,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Log order events
     */
    public function logOrderCreated($order): self
    {
        return $this->log("Order #{$order->id} created", [
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
        ]);
    }

    public function logOrderStatusChanged($order, string $oldStatus, string $newStatus): self
    {
        return $this->log("Order #{$order->id} status changed from {$oldStatus} to {$newStatus}", [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    /**
     * Log payment events
     */
    public function logPayment($transaction, string $status): self
    {
        return $this->log("Payment {$status} for transaction {$transaction->transaction_id}", [
            'transaction_id' => $transaction->transaction_id,
            'order_id' => $transaction->order_id,
            'amount' => $transaction->amount,
            'method' => $transaction->payment_method,
            'status' => $status,
        ]);
    }

    /**
     * Log admin actions
     */
    public function logAdminAction(string $action, array $data = []): self
    {
        return $this->log("Admin: {$action}", $data);
    }

    /**
     * Get activity logs
     */
    public function getLogs(array $filters = [], int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        $query = \Spatie\Activitylog\Models\Activity::query();

        if (!empty($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (!empty($filters['model_type'])) {
            $query->where('subject_type', $filters['model_type']);
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        return $query->latest()->limit($limit)->get();
    }

    /**
     * Export logs to CSV
     */
    public function exportToCsv(array $filters = [], string $filename = null): string
    {
        $filename = $filename ?? 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $logs = $this->getLogs($filters, 10000);

        $headers = ['ID', 'Description', 'User', 'IP', 'User Agent', 'Properties', 'Created At'];

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->description,
                $log->causer?->email ?? 'System',
                $log->ip_address ?? '',
                $log->user_agent ?? '',
                json_encode($log->properties),
                $log->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        Storage::disk('local')->put("exports/{$filename}", $csv);

        return $filename;
    }

    /**
     * Get statistics
     */
    public function getStats(string $period = 'day'): array
    {
        $query = \Spatie\Activitylog\Models\Activity::query();

        return [
            'total' => $query->count(),
            'today' => $query->whereDate('created_at', today())->count(),
            'this_week' => $query->where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => $query->where('created_at', '>=', now()->startOfMonth())->count(),
            'by_user' => $query->selectRaw('causer_id, COUNT(*) as count')
                ->groupBy('causer_id')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'causer_id'),
            'by_model' => $query->selectRaw('subject_type, COUNT(*) as count')
                ->whereNotNull('subject_type')
                ->groupBy('subject_type')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'subject_type'),
        ];
    }
}
