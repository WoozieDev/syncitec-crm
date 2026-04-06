<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pendiente',
        'completada',
    ];

    public const PRIORITIES = [
        'alta',
        'media',
        'baja',
    ];

    private const LEGACY_STATUS_MAP = [
        'backlog' => 'pendiente',
        'todo' => 'pendiente',
        'in_progress' => 'pendiente',
        'qa' => 'pendiente',
        'done' => 'completada',
        'pending' => 'pendiente',
        'completed' => 'completada',
        'en_progreso' => 'pendiente',
        'en_revision' => 'pendiente',
        'bloqueada' => 'pendiente',
    ];

    private const LEGACY_PRIORITY_MAP = [
        'urgente' => 'alta',
    ];

    private const STATUS_LABELS = [
        'pendiente' => 'Pendiente',
        'completada' => 'Completada',
    ];

    protected $fillable = [
        'project_id',
        'module_id',
        'title',
        'description',
        'status',
        'priority',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(ProjectModule::class, 'module_id');
    }

    public function scopeWhereKanbanStatus(Builder $query, string $status): Builder
    {
        return $query->whereIn('status', self::databaseStatusesForStatus($status));
    }

    public static function canonicalStatus(?string $status): ?string
    {
        $value = self::normalizeToken($status);

        if ($value === '') {
            return null;
        }

        return self::LEGACY_STATUS_MAP[$value]
            ?? (in_array($value, self::STATUSES, true) ? $value : null);
    }

    public static function normalizeStatus(?string $status): string
    {
        return self::canonicalStatus($status) ?? 'pendiente';
    }

    public static function isSupportedStatus(?string $status): bool
    {
        return self::canonicalStatus($status) !== null;
    }

    public static function canonicalPriority(?string $priority): ?string
    {
        $value = self::normalizeToken($priority);

        if ($value === '') {
            return null;
        }

        $normalized = self::LEGACY_PRIORITY_MAP[$value] ?? $value;

        return in_array($normalized, self::PRIORITIES, true) ? $normalized : null;
    }

    public static function normalizePriority(?string $priority): ?string
    {
        return self::canonicalPriority($priority);
    }

    public static function isSupportedPriority(?string $priority): bool
    {
        return self::canonicalPriority($priority) !== null;
    }

    /**
     * @return array<int, string>
     */
    public static function databaseStatusesForStatus(string $status): array
    {
        $normalized = self::canonicalStatus($status);

        if ($normalized === null) {
            return [];
        }

        return collect(self::LEGACY_STATUS_MAP)
            ->filter(fn (string $mappedStatus) => $mappedStatus === $normalized)
            ->keys()
            ->prepend($normalized)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function databasePrioritiesForPriority(string $priority): array
    {
        $normalized = self::normalizePriority($priority);

        if ($normalized === null) {
            return [];
        }

        return collect(self::LEGACY_PRIORITY_MAP)
            ->filter(fn (string $mappedPriority) => $mappedPriority === $normalized)
            ->keys()
            ->prepend($normalized)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function pendingDatabaseStatuses(): array
    {
        return self::databaseStatusesForStatus('pendiente');
    }

    /**
     * @return array<int, string>
     */
    public static function completedDatabaseStatuses(): array
    {
        return self::databaseStatusesForStatus('completada');
    }

    public static function statusLabel(string $status): string
    {
        $normalized = self::normalizeStatus($status);

        return self::STATUS_LABELS[$normalized] ?? Str::headline($normalized);
    }

    private static function normalizeToken(?string $value): string
    {
        return Str::of((string) $value)
            ->trim()
            ->lower()
            ->replace(' ', '_')
            ->toString();
    }
}
