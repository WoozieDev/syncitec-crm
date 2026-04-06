<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CalendarDays,
    CheckCircle2,
    Clock3,
    Flag,
    Pencil,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    dueDateTone,
    formatDate,
    formatLabel,
    priorityBadgeClasses,
    priorityDotClasses,
    statusBadgeClasses,
} from '@/modules/personalTasks/helpers';
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
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.12em] uppercase"
                            :class="
                                statusBadgeClasses[task.status] ??
                                'bg-muted text-muted-foreground'
                            "
                        >
                            {{ formatLabel(task.status) }}
                        </span>

                        <span
                            class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-[11px] font-bold uppercase"
                            :class="
                                priorityBadgeClasses[task.priority ?? ''] ??
                                'border-border/60 bg-muted text-muted-foreground'
                            "
                        >
                            <span
                                class="size-2 rounded-full"
                                :class="
                                    priorityDotClasses[task.priority ?? ''] ??
                                    'bg-muted-foreground/40'
                                "
                            />
                            {{ formatLabel(task.priority ?? 'sin prioridad') }}
                        </span>

                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-border/60 bg-background/80 px-3 py-1 text-[11px] font-bold uppercase"
                            :class="dueDateTone(task)"
                        >
                            <CalendarDays class="size-3.5" />
                            {{ formatDate(task.due_date, 'Sin fecha') }}
                        </span>
                    </div>

                    <div>
                        <h1
                            class="text-3xl font-black tracking-tight text-foreground sm:text-4xl xl:text-[3.2rem] xl:leading-none"
                        >
                            {{ task.title }}
                        </h1>
                        <p class="mt-2 text-base text-muted-foreground">
                            {{ task.is_completed ? 'Cerrada y archivada en historial reciente.' : 'Tarea activa dentro del planner personal.' }}
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
                            <span>Volver al planner</span>
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
                        Contexto enriquecido guardado para esta actividad.
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-6">
                    <div
                        class="personal-rich-content rounded-2xl border border-border/60 bg-muted/20 p-5 text-sm leading-7 text-foreground"
                        v-html="
                            task.description ??
                            '<p class=&quot;text-muted-foreground&quot;>Sin descripcion registrada.</p>'
                        "
                    />

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
                                Fecha objetivo
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ formatDate(task.due_date, 'Sin fecha') }}
                            </p>
                        </div>
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

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <p
                                class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                            >
                                Fecha objetivo
                            </p>
                            <p class="mt-2 text-xl font-black text-cyan-200">
                                {{ formatDate(task.due_date, 'Sin fecha') }}
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <p
                                class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                            >
                                Completado
                            </p>
                            <p class="mt-2 text-xl font-black text-emerald-200">
                                {{ task.is_completed ? 'Si' : 'No' }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="space-y-3 rounded-2xl border border-white/10 bg-black/15 p-4 text-sm"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-white/65">
                                <Flag class="size-4" />
                                Prioridad
                            </span>
                            <span class="font-semibold text-white">
                                {{ formatLabel(task.priority ?? 'sin prioridad') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-white/65">
                                <Clock3 class="size-4" />
                                Creada
                            </span>
                            <span class="font-semibold text-white">
                                {{ formatDate(task.created_at) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-white/65">
                                <CheckCircle2 class="size-4" />
                                Cierre
                            </span>
                            <span class="font-semibold text-white">
                                {{ formatDate(task.completed_at, 'Pendiente') }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
