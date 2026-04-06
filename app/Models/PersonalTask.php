<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PersonalTask extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pendiente',
        'en_progreso',
        'completada',
    ];

    public const PRIORITIES = [
        'alta',
        'media',
        'baja',
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'order' => 'integer',
        ];
    }

    public static function normalizeStatus(?string $status): string
    {
        $value = Str::of((string) $status)->trim()->lower()->replace(' ', '_')->toString();

        return in_array($value, self::STATUSES, true) ? $value : 'pendiente';
    }

    public static function normalizePriority(?string $priority): ?string
    {
        $value = Str::of((string) $priority)->trim()->lower()->replace(' ', '_')->toString();

        if ($value === '') {
            return null;
        }

        return in_array($value, self::PRIORITIES, true) ? $value : null;
    }

    public function markCompleted(): void
    {
        $this->forceFill([
            'status' => 'completada',
            'completed_at' => $this->completed_at ?? now(),
        ])->save();
    }

    public function markOpen(?string $status = null): void
    {
        $normalizedStatus = self::normalizeStatus($status);

        $this->forceFill([
            'status' => $normalizedStatus === 'completada' ? 'pendiente' : $normalizedStatus,
            'completed_at' => null,
        ])->save();
    }
}
