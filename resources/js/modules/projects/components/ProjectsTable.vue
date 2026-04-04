<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Eye,
    Pencil,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import ProjectStatusBadge from '@/modules/projects/components/ProjectStatusBadge.vue';
import type { PaginationLink, Project } from '@/modules/projects/types';
import { edit, show } from '@/routes/projects';

const props = defineProps<{
    items: Project[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
}>();

const emit = defineEmits<{
    (e: 'delete', project: Project): void;
}>();

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const previousLink = computed(() => props.links[0] ?? null);
const nextLink = computed(() => props.links[props.links.length - 1] ?? null);

const accentClasses: Record<string, string> = {
    planning: 'bg-slate-400 dark:bg-slate-500',
    in_progress: 'bg-blue-500 dark:bg-blue-400',
    in_review: 'bg-cyan-500 dark:bg-cyan-400',
    done: 'bg-emerald-500 dark:bg-emerald-400',
    canceled: 'bg-rose-500 dark:bg-rose-400',
};

const progressClasses: Record<string, string> = {
    planning: 'bg-slate-400 dark:bg-slate-500',
    in_progress: 'bg-blue-500 dark:bg-blue-400',
    in_review: 'bg-cyan-500 dark:bg-cyan-400',
    done: 'bg-emerald-500 dark:bg-emerald-400',
    canceled: 'bg-rose-500 dark:bg-rose-400',
};

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;

const textOrFallback = (
    value: string | null | undefined,
    fallback = 'Sin dato',
) => {
    if (!value || value.trim().length === 0) {
        return fallback;
    }

    return value;
};
</script>

<template>
    <section
        class="rounded-4xl border border-border/60 bg-muted/50 p-4 shadow-sm sm:p-6 dark:bg-slate-900/40"
    >
        <div
            class="hidden px-6 pb-3 lg:grid lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1.15fr)_minmax(0,0.9fr)_minmax(0,1.2fr)_minmax(0,0.8fr)_minmax(0,0.8fr)_auto]"
        >
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Proyecto
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Cliente
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Estado
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Avance
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Total
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Saldo
            </p>
            <p
                class="text-right text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Acciones
            </p>
        </div>

        <div class="space-y-4">
            <Card
                v-for="project in items"
                :key="project.id"
                class="gap-0 rounded-3xl border-border/40 py-0 transition-colors hover:bg-accent/30"
            >
                <div
                    class="grid grid-cols-1 gap-4 p-4 md:p-5 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1.15fr)_minmax(0,0.9fr)_minmax(0,1.2fr)_minmax(0,0.8fr)_minmax(0,0.8fr)_auto] lg:items-center lg:gap-3"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="mt-0.5 h-11 w-1 rounded-full"
                            :class="
                                accentClasses[project.status_tone] ??
                                accentClasses.planning
                            "
                        />

                        <div>
                            <Link
                                :href="show(project.id)"
                                class="text-sm font-bold text-foreground transition-colors hover:text-primary"
                            >
                                {{ project.name }}
                            </Link>
                            <p class="text-[11px] text-muted-foreground">
                                {{ project.project_code }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Cliente
                        </p>
                        <p class="text-sm font-medium text-foreground">
                            {{ project.client.display_name }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ textOrFallback(project.client.name, 'Sin contacto') }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Estado
                        </p>
                        <ProjectStatusBadge
                            :label="project.status_label"
                            :tone="project.status_tone"
                        />
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Avance
                        </p>
                        <div class="space-y-2">
                            <div
                                class="flex items-center justify-between text-xs font-semibold"
                            >
                                <span>{{ project.progress_percent }}%</span>
                                <span class="text-muted-foreground">
                                    {{ project.progress_label }}
                                </span>
                            </div>

                            <div class="h-2 w-full rounded-full bg-muted">
                                <div
                                    class="h-2 rounded-full transition-[width]"
                                    :class="
                                        progressClasses[project.status_tone] ??
                                        progressClasses.planning
                                    "
                                    :style="{
                                        width: `${project.progress_percent}%`,
                                    }"
                                />
                            </div>
                        </div>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Total
                        </p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ formatCurrency(project.total_amount) }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Saldo
                        </p>
                        <p
                            class="text-sm font-semibold"
                            :class="
                                project.balance_due === 0
                                    ? 'text-emerald-600 dark:text-emerald-300'
                                    : 'text-foreground'
                            "
                        >
                            {{ formatCurrency(project.balance_due) }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <Link
                            :href="show(project.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Ver detalle"
                        >
                            <Eye class="size-4" />
                        </Link>

                        <Link
                            :href="edit(project.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Editar"
                        >
                            <Pencil class="size-4" />
                        </Link>

                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                            title="Eliminar"
                            @click="emit('delete', project)"
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
                    No se encontraron proyectos con los filtros actuales.
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
                de {{ total.toLocaleString('es-ES') }} proyectos
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
