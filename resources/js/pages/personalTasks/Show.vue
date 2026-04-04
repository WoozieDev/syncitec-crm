<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CalendarDays,
    CheckSquare,
    Pencil,
    TimerReset,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { PersonalTaskShowProps } from '@/modules/personalTasks/types';
import { edit, index } from '@/routes/personal-tasks';

defineProps<PersonalTaskShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tareas personales',
                href: index(),
            },
            {
                title: 'Detalle de tarea',
                href: '#',
            },
        ],
    },
});

const statusClasses: Record<string, string> = {
    pendiente:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    en_progreso:
        'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200',
    en_revision:
        'bg-cyan-500/15 text-cyan-700 dark:bg-cyan-400/15 dark:text-cyan-200',
    completada:
        'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
};

const priorityClasses: Record<string, string> = {
    alta: 'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
    media: 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200',
    baja: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
};

const textOrFallback = (
    value: string | null | undefined,
    fallback = 'Sin dato',
): string => {
    if (!value || value.trim().length === 0) {
        return fallback;
    }

    return value;
};

const parseDate = (value?: string | null) => {
    if (!value) {
        return null;
    }

    const normalized = value.includes(' ')
        ? value.replace(' ', 'T')
        : `${value}T00:00:00`;
    const parsedDate = new Date(normalized);

    return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
};

const formatDate = (value?: string | null): string => {
    const parsedDate = parseDate(value);

    if (!parsedDate) {
        return '-';
    }

    return parsedDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const formatLabel = (value: string | null | undefined): string => {
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
</script>

<template>
    <Head :title="`Tarea - ${task.title}`" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div
                class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between"
            >
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.12em] uppercase"
                            :class="
                                statusClasses[task.status] ??
                                statusClasses.pendiente
                            "
                        >
                            {{ formatLabel(task.status) }}
                        </span>

                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.12em] uppercase"
                            :class="
                                priorityClasses[
                                    (task.priority ?? '').toLowerCase()
                                ] ?? 'bg-muted text-muted-foreground'
                            "
                        >
                            {{ formatLabel(task.priority ?? 'sin prioridad') }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h1
                            class="text-3xl font-black tracking-tight text-foreground sm:text-4xl xl:text-[3.2rem] xl:leading-none"
                        >
                            {{ task.title }}
                        </h1>
                        <p
                            class="text-base font-medium text-muted-foreground sm:text-lg"
                        >
                            Orden interno: {{ task.order }}
                        </p>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                    <Link :href="index()">
                        <Button
                            variant="outline"
                            class="w-full cursor-pointer sm:w-auto"
                        >
                            <ArrowLeft class="size-4" />
                            <span>Volver al listado</span>
                        </Button>
                    </Link>

                    <Link :href="edit(task.id)">
                        <Button class="w-full cursor-pointer sm:w-auto">
                            <Pencil class="size-4" />
                            <span>Editar tarea</span>
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Descripcion de la tarea</CardTitle>
                    <CardDescription>
                        Contenido operativo de esta tarea personal.
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-6">
                    <p class="text-sm leading-relaxed text-muted-foreground">
                        {{ textOrFallback(task.description, 'Sin descripcion registrada.') }}
                    </p>

                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                            >
                                Estado
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ formatLabel(task.status) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                            >
                                Prioridad
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ formatLabel(task.priority ?? 'sin prioridad') }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                            >
                                Orden
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ task.order }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-border/60 bg-muted/25 p-4 text-sm text-muted-foreground"
                    >
                        Creada el {{ formatDate(task.created_at) }} y actualizada
                        el {{ formatDate(task.updated_at) }}.
                    </div>
                </CardContent>
            </Card>

            <section
                class="relative overflow-hidden rounded-3xl border border-blue-400/20 bg-linear-to-br from-blue-600 via-blue-700 to-slate-950 p-6 text-white shadow-xl shadow-blue-950/20"
            >
                <div
                    class="absolute -top-20 -right-12 h-44 w-44 rounded-full bg-white/10 blur-3xl"
                />
                <div
                    class="absolute -bottom-20 -left-12 h-40 w-40 rounded-full bg-cyan-300/10 blur-3xl"
                />

                <div class="relative space-y-6">
                    <div>
                        <p
                            class="text-xs font-semibold tracking-[0.22em] text-white/65 uppercase"
                        >
                            Resumen operativo
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-white/70">Estado actual</p>
                        <p class="mt-1 text-2xl font-black tracking-tight">
                            {{ formatLabel(task.status) }}
                        </p>
                        <p class="mt-1 text-sm text-white/70">
                            Prioridad: {{ formatLabel(task.priority ?? 'sin prioridad') }}
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <p
                                class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                            >
                                Orden
                            </p>
                            <p class="mt-2 text-xl font-black text-cyan-200">
                                {{ task.order }}
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <p
                                class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                            >
                                Revision
                            </p>
                            <p class="mt-2 text-xl font-black text-rose-200">
                                {{ formatDate(task.updated_at) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="space-y-2 rounded-2xl border border-white/10 bg-black/15 p-4 text-sm"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Creacion</span>
                            <span class="font-semibold text-white">
                                {{ formatDate(task.created_at) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Ultima actualizacion</span>
                            <span class="font-semibold text-white">
                                {{ formatDate(task.updated_at) }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <Card class="border-border/60">
            <CardHeader>
                <CardTitle>Metadatos de la tarea</CardTitle>
                <CardDescription>
                    Valores reales almacenados para seguimiento personal.
                </CardDescription>
            </CardHeader>

            <CardContent class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Titulo
                    </p>
                    <p
                        class="mt-1 flex items-center gap-2 text-sm font-semibold text-foreground"
                    >
                        <CheckSquare class="size-4 text-muted-foreground" />
                        {{ task.title }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Estado
                    </p>
                    <p class="mt-1 text-sm font-semibold text-foreground">
                        {{ formatLabel(task.status) }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Prioridad
                    </p>
                    <p class="mt-1 text-sm font-semibold text-foreground">
                        {{ formatLabel(task.priority ?? 'sin prioridad') }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Orden
                    </p>
                    <p
                        class="mt-1 flex items-center gap-2 text-sm font-semibold text-foreground"
                    >
                        <TimerReset class="size-4 text-muted-foreground" />
                        {{ task.order }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Creada
                    </p>
                    <p
                        class="mt-1 flex items-center gap-2 text-sm font-semibold text-foreground"
                    >
                        <CalendarDays class="size-4 text-muted-foreground" />
                        {{ formatDate(task.created_at) }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Actualizada
                    </p>
                    <p
                        class="mt-1 flex items-center gap-2 text-sm font-semibold text-foreground"
                    >
                        <CalendarDays class="size-4 text-muted-foreground" />
                        {{ formatDate(task.updated_at) }}
                    </p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
