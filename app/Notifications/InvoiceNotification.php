<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected User $owner,
        protected Company $company,
        protected Subscription $subscription,
        protected Payment $payment,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Subscription is Expiring Soon')
            ->line("Hi {$this->company->name},")
            ->line("Just a friendly reminder that your subscription to {$this->subscription->plan->name} will expire on {$this->subscription->end_date->format('M d, Y')}.")
            ->line('To ensure uninterrupted access to your inventory management tools, please renew your subscription before then.')
            ->line('Here are your invoice details:')
            ->line("Amount Due: {$this->payment->amount}")
            ->line("Due Date: {$this->subscription->end_date->format('M d, Y')}")
            ->line('You can view your invoice by clicking the button below:')
            ->action('Subscription', route('filament.app.pages.subscriptions', [$this->company]))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
