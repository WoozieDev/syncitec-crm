const COLUMN_TONES = [
    {
        surface: 'border-slate-500/15 bg-slate-500/5',
        title: 'text-slate-700 dark:text-slate-200',
        badge: 'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    },
    {
        surface: 'border-blue-500/15 bg-blue-500/5',
        title: 'text-blue-700 dark:text-blue-200',
        badge: 'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200',
    },
    {
        surface: 'border-amber-500/15 bg-amber-500/5',
        title: 'text-amber-700 dark:text-amber-200',
        badge: 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200',
    },
    {
        surface: 'border-emerald-500/15 bg-emerald-500/5',
        title: 'text-emerald-700 dark:text-emerald-200',
        badge: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
    },
    {
        surface: 'border-rose-500/15 bg-rose-500/5',
        title: 'text-rose-700 dark:text-rose-200',
        badge: 'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
    },
    {
        surface: 'border-cyan-500/15 bg-cyan-500/5',
        title: 'text-cyan-700 dark:text-cyan-200',
        badge: 'bg-cyan-500/15 text-cyan-700 dark:bg-cyan-400/15 dark:text-cyan-200',
    },
];

export const taskStatusClasses: Record<string, string> = {
    pendiente:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    completada:
        'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
};

export const taskPriorityClasses: Record<string, string> = {
    alta: 'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
    media: 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200',
    baja: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
};

export const kanbanColumnSurfaceClass = (index: number): string =>
    COLUMN_TONES[index % COLUMN_TONES.length]?.surface ??
    'border-border/60 bg-muted/20';

export const kanbanColumnTitleClass = (index: number): string =>
    COLUMN_TONES[index % COLUMN_TONES.length]?.title ?? 'text-foreground';

export const kanbanColumnBadgeClass = (index: number): string =>
    COLUMN_TONES[index % COLUMN_TONES.length]?.badge ??
    'bg-muted text-muted-foreground';

export const formatTaskLabel = (value: string | null | undefined): string => {
    if (!value) {
        return 'Sin dato';
    }

    return value
        .replaceAll('_', ' ')
        .split(' ')
        .map((part) =>
            part.length > 0
                ? part.charAt(0).toUpperCase() + part.slice(1)
                : part,
        )
        .join(' ');
};

export const textOrFallback = (
    value: string | null | undefined,
    fallback = 'Sin dato',
): string => {
    if (!value || value.trim().length === 0) {
        return fallback;
    }

    return value;
};
