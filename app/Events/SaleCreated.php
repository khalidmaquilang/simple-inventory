<?php

namespace App\Events;

use App\Enums\StockMovementEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $companyId,
        public int $saleId,
        public $saleDate,
        public int $productId,
        public string $sku,
        public string $name,
        public int $quantity,
        public float $unitCost,
        public int $userId,
        public ?string $customerId = null,
        public string $referenceNumber = '',
        public StockMovementEnum $type = StockMovementEnum::SALE,
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
