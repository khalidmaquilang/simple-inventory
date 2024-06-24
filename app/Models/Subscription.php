<?php

namespace App\Models;

use App\Enums\BillingCycleEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\SubscriptionStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'extra_users',
        'total_amount',
    ];

    /**
     * @var \class-string[]
     */
    protected $casts = [
        'status' => SubscriptionStatusEnum::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'plan',
    ];

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status === SubscriptionStatusEnum::ACTIVE;
    }

    /**
     * @return void
     */
    public function updateEndDate(): void
    {
        $this->end_date = $this->plan->billing_cycle === BillingCycleEnum::MONTHLY ? (new Carbon($this->end_date))->addMonth() : (new Carbon($this->end_date))->addYear();
        $this->save();
    }

    /**
     * @return void
     */
    public function cancel(): void
    {
        $this->status = SubscriptionStatusEnum::CANCELED;
        $this->save();
    }

    /**
     * @param  PaymentStatusEnum  $status
     * @param  Carbon|null  $dueDate
     * @return Model
     */
    public function createPayment(PaymentStatusEnum $status, ?Carbon $dueDate = null): Model
    {
        $latestPayment = $this->payments()->latest()->first();
        if (! empty($latestPayment) && $latestPayment->status === PaymentStatusEnum::PENDING) {
            $latestPayment->status = PaymentStatusEnum::FAILED;
            $latestPayment->save();
        }

        return $this->payments()->create([
            'company_id' => $this->company_id,
            'payment_date' => now(),
            'due_date' => $dueDate ?? $this->end_date,
            'amount' => $this->total_amount,
            'payment_method' => 'system',
            'status' => $status,
        ]);
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
