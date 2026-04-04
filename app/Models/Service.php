<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    public const BILLING_TYPES = [
        'unico',
        'recurrente',
    ];

    public const BILLING_CYCLES = [
        'mensual',
        'trimestral',
        'semestral',
        'anual',
    ];

    public const STATUSES = [
        'activo',
        'pendiente',
        'suspendido',
        'cancelado',
    ];

    protected $fillable = [
        'client_id',
        'service_type_id',
        'provider_id',
        'name',
        'description',
        'billing_type',
        'billing_cycle',
        'price',
        'cost',
        'start_date',
        'next_renewal_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'start_date' => 'date',
            'next_renewal_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ServicePayment::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $subQuery) use ($search) {
            $subQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('billing_type', 'like', "%{$search}%")
                ->orWhere('billing_cycle', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('client', function (Builder $clientQuery) use ($search) {
                    $clientQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('serviceType', fn (Builder $typeQuery) => $typeQuery->where('name', 'like', "%{$search}%"))
                ->orWhereHas('provider', fn (Builder $providerQuery) => $providerQuery->where('name', 'like', "%{$search}%"));
        });
    }
}
