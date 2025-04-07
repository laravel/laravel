<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Quote;

class QuoteStatusChanged extends Notification
{
    use Queueable;

    protected $quote;

    /**
     * Create a new notification instance.
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusText = match($this->quote->status) {
            'pending' => 'قيد المراجعة',
            'agency_approved' => 'تمت الموافقة من قبل الوكالة',
            'customer_approved' => 'تم قبوله من قبل العميل',
            'agency_rejected' => 'تم رفضه من قبل الوكالة',
            'customer_rejected' => 'تم رفضه من قبل العميل',
            default => $this->quote->status,
        };

        // تخصيص الرسالة حسب نوع المستخدم
        $subject = "تحديث حالة عرض السعر #" . $this->quote->id;
        $introMessage = "تم تحديث حالة عرض السعر الخاص بطلب '" . $this->quote->request->service->name . "' إلى: " . $statusText;
        
        // تخصيص إضافي حسب الحالة
        if ($this->quote->status === 'agency_rejected' && $notifiable->id === $this->quote->subagent_id) {
            $introMessage .= "\nسبب الرفض: " . $this->quote->rejection_reason;
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->line($introMessage)
                    ->line("سعر العرض: " . $this->quote->price . " " . $this->quote->currency_code)
                    ->action('عرض التفاصيل', $this->getActionUrl($notifiable))
                    ->line('شكراً لاستخدامك نظام وكالات السفر!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'quote_id' => $this->quote->id,
            'request_id' => $this->quote->request_id,
            'status' => $this->quote->status,
            'price' => $this->quote->price,
            'currency_code' => $this->quote->currency_code,
            'rejection_reason' => $this->quote->rejection_reason,
        ];
    }

    /**
     * تحديد URL العمل بناءً على نوع المستخدم
     */
    private function getActionUrl($notifiable): string
    {
        if ($notifiable->user_type === 'subagent') {
            return route('subagent.quotes.show', $this->quote->id);
        } elseif ($notifiable->user_type === 'customer') {
            return route('customer.quotes.show', $this->quote->id);
        } else {
            return route('agency.quotes.show', $this->quote->id);
        }
    }
}
