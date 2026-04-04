<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Eye,
    FolderKanban,
    Pencil,
    SquareKanban,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import type {
    PaginationLink,
    ProjectTask,
} from '@/modules/projectTasks/types';
import { edit, show } from '@/routes/project-tasks';
import { show as showProject } from '@/routes/projects';

const props = defineProps<{
    items: ProjectTask[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
}>();

const emit = defineEmits<{
    (e: 'delete', task: ProjectTask): void;
}>();

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const previousLink = computed(() => props.links[0] ?? null);
const nextLink = computed(() => props.links[props.links.length - 1] ?? null);

const accentClasses: Record<string, string> = {
    pendiente: 'bg-slate-400 dark:bg-slate-500',
    en_progreso: 'bg-blue-500 dark:bg-blue-400',
    en_revision: 'bg-cyan-500 dark:bg-cyan-400',
    completada: 'bg-emerald-500 dark:bg-emerald-400',
};

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
) => {
    if (!value || value.trim().length === 0) {
        return fallback;
    }

    return value;
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
    <section
        class="rounded-4xl border border-border/60 bg-muted/50 p-4 shadow-sm sm:p-6 dark:bg-slate-900/40"
    >
        <div class="space-y-4">
            <Card
                v-for="task in items"
                :key="task.id"
                class="gap-0 rounded-3xl border-border/40 py-0 transition-colors hover:bg-accent/30"
            >
                <div
                    class="grid grid-cols-1 gap-4 p-4 md:p-5 xl:grid-cols-[minmax(0,1.45fr)_minmax(0,1.15fr)_minmax(0,0.95fr)_minmax(0,0.9fr)_minmax(0,0.55fr)_auto] xl:items-center xl:gap-3"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="mt-0.5 h-12 w-1 rounded-full"
                            :class="
                                accentClasses[task.status] ??
                                accentClasses.pendiente
                            "
                        />

                        <div class="min-w-0">
                            <Link
                                :href="show(task.id)"
                                class="text-sm font-bold text-foreground transition-colors hover:text-primary"
                            >
                                {{ task.title }}
                            </Link>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ textOrFallback(task.description, 'Sin descripcion.') }}
                            </p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
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
                        </div>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase xl:hidden"
                        >
                            Proyecto
                        </p>
                        <div class="space-y-1">
                            <Link
                                v-if="task.project"
                                :href="showProject(task.project.id)"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-foreground transition-colors hover:text-primary"
                            >
                                <FolderKanban class="size-4 text-muted-foreground" />
                                <span>{{ task.project.name }}</span>
                            </Link>
                            <p v-else class="text-sm font-semibold text-foreground">
                                Sin proyecto
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ task.project?.client.display_name ?? 'Sin cliente' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase xl:hidden"
                        >
                            Modulo
                        </p>
                        <p
                            class="flex items-center gap-2 text-sm text-foreground"
                        >
                            <SquareKanban class="size-4 text-muted-foreground" />
                            {{ task.module?.name ?? 'Sin modulo' }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase xl:hidden"
                        >
                            Prioridad
                        </p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ formatLabel(task.priority ?? 'sin prioridad') }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase xl:hidden"
                        >
                            Orden
                        </p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ task.order }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <Link
                            :href="show(task.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Ver detalle"
                        >
                            <Eye class="size-4" />
                        </Link>

                        <Link
                            :href="edit(task.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Editar"
                        >
                            <Pencil class="size-4" />
                        </Link>

                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                            title="Eliminar"
                            @click="emit('delete', task)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>
            </Card>

            <Card
                v-if="items.length === 0"
                class="rounded-3xl border-dashed py-10"
            >
                <p class="text-center text-sm text-muted-foreground">
                    No se encontraron tareas con los filtros actuales.
                </p>
            </Card>
        </div>

        <div
            class="mt-6 flex flex-col gap-4 text-xs font-medium text-muted-foreground md:mt-8 md:flex-row md:items-center md:justify-between"
        >
            <p>
                Mostrando
                <span class="font-bold text-foreground"
                    >{{ from }} - {{ to }}</span
                >
                de {{ total.toLocaleString('es-ES') }} tareas
            </p>

            <div class="flex items-center gap-1">
                <Link
                    v-if="previousLink?.url"
                    :href="previousLink.url"
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-background hover:text-foreground"
                >
                    <ChevronLeft class="size-4" />
                </Link>
                <span
                    v-else
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground/40"
                >
                    <ChevronLeft class="size-4" />
                </span>

                <template v-for="link in numericLinks" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-xs font-bold transition-colors"
                        :class="
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:bg-background hover:text-foreground'
                        "
                    >
                        {{ link.label }}
                    </Link>
                </template>

                <Link
                    v-if="nextLink?.url"
                    :href="nextLink.url"
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-background hover:text-foreground"
                >
                    <ChevronRight class="size-4" />
                </Link>
                <span
                    v-else
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground/40"
                >
                    <ChevronRight class="size-4" />
                </span>
            </div>
        </div>
    </section>
</template>
