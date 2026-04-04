<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $subQuery) use ($search) {
            $subQuery
                ->where('payment_method', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%")
                ->orWhereHas('service', function (Builder $serviceQuery) use ($search) {
                    $serviceQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhereHas('client', function (Builder $clientQuery) use ($search) {
                            $clientQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('company', 'like', "%{$search}%");
                        })
                        ->orWhereHas(
                            'serviceType',
                            fn (Builder $typeQuery) => $typeQuery->where('name', 'like', "%{$search}%")
                        )
                        ->orWhereHas(
                            'provider',
                            fn (Builder $providerQuery) => $providerQuery->where('name', 'like', "%{$search}%")
                        );
                });
        });
    }
}
