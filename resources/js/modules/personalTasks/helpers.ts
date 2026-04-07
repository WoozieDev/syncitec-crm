import type { PersonalTask } from './types';

export const priorityBadgeClasses: Record<string, string> = {
    alta: 'border-rose-500/30 bg-rose-500/10 text-rose-700 dark:text-rose-200',
    media: 'border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-200',
    baja: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-200',
};

export const priorityDotClasses: Record<string, string> = {
    alta: 'bg-rose-500',
    media: 'bg-amber-500',
    baja: 'bg-emerald-500',
};

export const statusBadgeClasses: Record<string, string> = {
    pendiente:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    en_progreso:
        'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200',
    completada:
        'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
};

export const taskGroupClasses: Record<string, string> = {
    overdue: 'border-rose-500/30 bg-rose-500/5',
    today: 'border-amber-500/30 bg-amber-500/5',
    this_week: 'border-blue-500/20 bg-blue-500/5',
    next_week: 'border-emerald-500/20 bg-emerald-500/5',
    backlog: 'border-border/60 bg-muted/20',
};

export const formatLabel = (value: string | null | undefined): string => {
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

export const stripHtml = (value: string | null | undefined): string => {
    if (!value) {
        return '';
    }

    if (typeof window === 'undefined') {
        return value
            .replace(/<[^>]+>/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    const element = document.createElement('div');
    element.innerHTML = value;

    return element.textContent?.replace(/\s+/g, ' ').trim() ?? '';
};

export const textPreview = (
    value: string | null | undefined,
    fallback = 'Sin descripcion registrada.',
): string => {
    const text = stripHtml(value);

    return text.length > 0 ? text : fallback;
};

export const parseDate = (value?: string | null) => {
    if (!value) {
        return null;
    }

    const normalized = value.includes(' ')
        ? value.replace(' ', 'T')
        : `${value}T00:00:00`;
    const parsedDate = new Date(normalized);

    return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
};

export const formatDate = (value?: string | null, fallback = '-'): string => {
    const parsedDate = parseDate(value);

    if (!parsedDate) {
        return fallback;
    }

    return parsedDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

export const dueDateTone = (task: PersonalTask): string => {
    if (!task.due_date) {
        return 'text-muted-foreground';
    }

    const dueDate = parseDate(task.due_date);
    const today = parseDate(new Date().toISOString().slice(0, 10));

    if (!dueDate || !today) {
        return 'text-muted-foreground';
    }

    if (dueDate < today) {
        return 'text-rose-600 dark:text-rose-300';
    }

    if (dueDate.getTime() === today.getTime()) {
        return 'text-amber-600 dark:text-amber-300';
    }

    return 'text-foreground';
};
