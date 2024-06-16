<?php

namespace App\Events;

use App\Enums\GoodsIssueTypeEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoodsIssueCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $productId,
        public int $quantity,
        public int $userId,
        public GoodsIssueTypeEnum $type,
        public ?string $customerId = null,
        public ?string $supplierId = null,
        public string $referenceNumber = '',
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
