<?php

namespace App\Models;

use App\Enums\SubscriptionStatusEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model implements HasCurrentTenantLabel
{
    use HasFactory;

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getCompanyLogo(): string
    {
        $logo = $this->logo;
        if (empty($logo)) {
            return '';
        }

        return storage_path('app/public/'.$logo);
    }

    /**
     * @return array
     */
    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->lazy()
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                ->required(),
            TextInput::make('slug')
                ->required()
                ->alphaDash()
                ->prohibitedIf('slug', 'admin')
                ->unique(ignoreRecord: true)
                ->mutateStateForValidationUsing(fn (string $state) => strtolower($state)),
            TextInput::make('phone')
                ->tel()
                ->required(),
            TextInput::make('email')
                ->email()
                ->required(),
            Textarea::make('address')
                ->required(),
            FileUpload::make('logo')
                ->image()
                ->directory('companies/'.auth()->user()->id)
                ->maxSize(2048),
            Select::make('currency')
                ->options(Currency::getCurrencyList())
                ->required(),
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @return HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * @return HasMany
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * @return HasMany
     */
    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * @return HasMany
     */
    public function paymentTypes(): HasMany
    {
        return $this->hasMany(PaymentType::class);
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * @return HasMany
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * @return HasMany
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * @return HasMany
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return string
     */
    public function getCurrentTenantLabel(): string
    {
        return 'Active Company';
    }

    /**
     * @return Model|null
     */
    public function getActiveSubscription(): ?Model
    {
        return $this
            ->subscriptions()
            ->where('status', SubscriptionStatusEnum::ACTIVE)
            ->first();
    }

    /**
     * @return bool
     */
    public function hasReachedMaxUsers(): bool
    {
        $max = 1;
        $subscription = $this->getActiveSubscription();
        if (! empty($subscription)) {
            $max = $subscription->plan->max_users;
        }

        return $this->members()->count() >= $max;
    }

    /**
     * @return bool
     */
    public function hasReachedMaxRoles(): bool
    {
        $max = 0;
        $subscription = $this->getActiveSubscription();
        if (! empty($subscription)) {
            $max = $subscription->plan->max_roles;
        }

        return $this->roles()->count() >= $max;
    }

    /**
     * @return bool
     */
    public function hasReachedMaxPurchaseOrders(): bool
    {
        $max = 10;
        $subscription = $this->getActiveSubscription();
        if (! empty($subscription)) {
            $max = $subscription->plan->max_monthly_purchase_order;
        }

        $count = $this->purchaseOrders()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return $count >= $max;
    }

    /**
     * @return bool
     */
    public function hasReachedMaxSales(): bool
    {
        $max = 10;
        $subscription = $this->getActiveSubscription();
        if (! empty($subscription)) {
            $max = $subscription->plan->max_monthly_sale_order;
        }

        $count = $this->sales()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return $count >= $max;
    }
}
