<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    public function __construct(public Order $order) {}

    // Kirim via channel 'database' — disimpan ke tabel `notifications` di DB.
    // Tidak membutuhkan konfigurasi email/SMS; admin baca dari halaman dashboard.
    public function via($notifiable): array
    {
        return ['database'];
    }

    // Data yang disimpan di kolom `data` (JSON) tabel notifications.
    // Diakses nanti di Blade via $notif->data['order_code'], dsb.
    public function toDatabase($notifiable): array
    {
        return [
            'order_id'      => $this->order->id,
            'order_code'    => $this->order->code,
            'customer_name' => $this->order->customer_name,
            'total'         => $this->order->total,
        ];
    }
}
