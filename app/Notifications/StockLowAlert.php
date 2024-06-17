<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockLowAlert extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Inventory $inventory)
    {
        //
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
        return (new MailMessage)
            ->subject("Heads Up! {$this->inventory->product->name} is Running Low")
            ->line("Just a friendly reminder that {$this->inventory->product->name} is running low in your inventory.")
            ->line('You might want to stock up soon to avoid missing out on sales opportunities.')
            ->line('Check out the details:')
            ->line("SKU: {$this->inventory->product->sku}")
            ->line("Current Stock: {$this->inventory->quantity_on_hand}")
            ->line('Let us know if you have any questions.');
    }

    /**
     * @param  object  $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title('Low Stock Alert')
            ->body(
                "Product '{$this->inventory->product->name}' (SKU: {$this->inventory->product->sku}) is running low on stock."
            )
            ->icon('heroicon-o-exclamation-triangle')
            ->warning()
            ->getDatabaseMessage();
    }
}
