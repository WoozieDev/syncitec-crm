<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    CalendarRange,
    CheckCircle2,
    Filter,
    Plus,
    Search,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import PersonalTasksTable from '@/modules/personalTasks/components/PersonalTasksTable.vue';
import { usePersonalTasksIndex } from '@/modules/personalTasks/composables/usePersonalTasksIndex';
import type { PersonalTaskIndexProps } from '@/modules/personalTasks/types';
import { create, index } from '@/routes/personal-tasks';

const props = defineProps<PersonalTaskIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tareas personales',
                href: index(),
            },
        ],
    },
});

const {
    groupedTasks,
    overview,
    priorityOptions,
    search,
    priority,
    completion,
    handleDelete,
    toggleCompletion,
} = usePersonalTasksIndex(props);
</script>

<template>
    <Head title="Tareas personales" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div class="flex flex-col gap-6">
                <div
                    class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between"
                >
                    <div class="space-y-4">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/10 px-3 py-1.5 text-sm text-primary"
                        >
                            <CalendarRange class="size-4" />
                            <span class="font-semibold">
                                {{
                                    overview.open_tasks.toLocaleString('es-ES')
                                }}
                                activas
                            </span>
                        </div>

                        <div>
                            <h1
                                class="text-3xl font-black tracking-tight text-foreground sm:text-4xl"
                            >
                                Planner personal
                            </h1>
                            <p
                                class="mt-2 max-w-3xl text-sm text-muted-foreground"
                            >
                                Vista ligera tipo Asana para decidir que haces
                                hoy, que va esta semana y que se puede empujar a
                                la siguiente.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end xl:max-w-2xl"
                    >
                        <div class="relative w-full sm:max-w-sm">
                            <Search
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar por titulo, descripcion o prioridad"
                                class="h-11 w-full rounded-full border border-border/60 bg-background/70 pr-4 pl-10 text-sm transition outline-none focus:border-primary/20 focus-visible:ring-2 focus-visible:ring-primary/20"
                            />
                        </div>

                        <Link :href="create()">
                            <Button
                                class="h-11 w-full cursor-pointer rounded-xl px-5 font-semibold shadow-sm shadow-primary/20 sm:w-auto"
                            >
                                <Plus class="size-4" />
                                <span>Nueva tarea</span>
                            </Button>
                        </Link>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-6">
                    <article
                        class="rounded-2xl border border-border/60 bg-background/75 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Total tareas
                        </p>
                        <p class="mt-2 text-3xl font-black tracking-tight">
                            {{ overview.total_tasks.toLocaleString('es-ES') }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-rose-500/20 bg-rose-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Atrasadas
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-rose-700 dark:text-rose-200"
                        >
                            {{ overview.overdue }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-amber-500/20 bg-amber-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Hoy
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-amber-700 dark:text-amber-200"
                        >
                            {{ overview.today }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-blue-500/20 bg-blue-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Esta semana
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-blue-700 dark:text-blue-200"
                        >
                            {{ overview.this_week }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Proxima semana
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-emerald-700 dark:text-emerald-200"
                        >
                            {{ overview.next_week }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-border/60 bg-muted/50 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Backlog
                        </p>
                        <p class="mt-2 text-3xl font-black tracking-tight">
                            {{ overview.backlog }}
                        </p>
                    </article>
                </div>

                <div
                    class="flex flex-col gap-4 rounded-2xl border border-border/60 bg-background/75 p-4"
                >
                    <div class="flex items-center gap-2 text-muted-foreground">
                        <Filter class="size-4" />
                        <span
                            class="text-xs font-semibold tracking-[0.15em] uppercase"
                        >
                            Filtros
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors"
                            :class="
                                completion === ''
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'
                            "
                            @click="completion = ''"
                        >
                            Todo
                        </button>
                        <button
                            type="button"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors"
                            :class="
                                completion === 'open'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'
                            "
                            @click="completion = 'open'"
                        >
                            Abiertas
                        </button>
                        <button
                            type="button"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors"
                            :class="
                                completion === 'completed'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'
                            "
                            @click="completion = 'completed'"
                        >
                            Completadas
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors"
                            :class="
                                priority === ''
                                    ? 'bg-primary/10 text-primary'
                                    : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'
                            "
                            @click="priority = ''"
                        >
                            Todas las prioridades
                        </button>
                        <button
                            v-for="option in priorityOptions"
                            :key="option.value"
                            type="button"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors"
                            :class="
                                priority === option.value
                                    ? 'bg-primary/10 text-primary'
                                    : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'
                            "
                            @click="priority = option.value"
                        >
                            {{ option.label }}
                        </button>
                    </div>

                    <div
                        class="flex items-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 px-4 py-3 text-sm text-muted-foreground"
                    >
                        <CheckCircle2 class="size-4 text-emerald-600" />
                        <span>
                            Marca tareas como completadas sin salir de la lista
                            y usa los filtros para enfocarte rapido.
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <PersonalTasksTable
            :grouped-tasks="groupedTasks"
            @delete="handleDelete"
            @toggle-completion="toggleCompletion($event.task, $event.value)"
        />
    </div>
</template>
