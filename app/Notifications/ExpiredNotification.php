<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpiredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Company $company,
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
        $owner = $this->company->owner;

        return (new MailMessage)
            ->subject('Your Subscription Has Expired - Renew NOW!')
            ->line("Hi {$owner->name},")
            ->line("Just a friendly reminder that your {$this->company->name}'s subscription is now expired.")
            ->line('We are reverting your subscription plan to Freemium.')
            ->line('To ensure uninterrupted access to your inventory management tools, please upgrade your subscription.')
            ->line("If you have any questions or need assistance, please don't hesitate to contact.")
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
