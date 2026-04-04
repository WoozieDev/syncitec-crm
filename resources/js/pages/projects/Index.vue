<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { FolderKanban, Plus, Search } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import ProjectsTable from '@/modules/projects/components/ProjectsTable.vue';
import { useProjectsIndex } from '@/modules/projects/composables/useProjectsIndex';
import type { ProjectIndexProps } from '@/modules/projects/types';
import { create, index } from '@/routes/projects';

const props = defineProps<ProjectIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Proyectos',
                href: index(),
            },
        ],
    },
});

const {
    projects,
    overview,
    views,
    search,
    currentView,
    totalProjects,
    pageFrom,
    pageTo,
    handleDelete,
} = useProjectsIndex(props);

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;
</script>

<template>
    <Head title="Proyectos" />

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
                            <FolderKanban class="size-4" />
                            <span class="font-semibold">
                                {{ totalProjects.toLocaleString('es-ES') }}
                                proyectos
                            </span>
                        </div>

                        <div>
                            <h1
                                class="text-3xl font-black tracking-tight text-foreground sm:text-4xl"
                            >
                                Control de proyectos
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                                Gestiona el avance, el saldo pendiente y el
                                estado operativo de cada proyecto desde un solo
                                lugar.
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
                                placeholder="Buscar por proyecto, descripcion o cliente"
                                class="h-11 w-full rounded-full border border-border/60 bg-background/70 pr-4 pl-10 text-sm transition outline-none focus:border-primary/20 focus-visible:ring-2 focus-visible:ring-primary/20"
                            />
                        </div>

                        <Link :href="create()">
                            <Button
                                class="h-11 w-full cursor-pointer rounded-xl px-5 font-semibold shadow-sm shadow-primary/20 sm:w-auto"
                            >
                                <Plus class="size-4" />
                                <span>Nuevo proyecto</span>
                            </Button>
                        </Link>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <article
                        class="rounded-2xl border border-border/60 bg-background/75 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Valor total
                        </p>
                        <p class="mt-2 text-3xl font-black tracking-tight">
                            {{ formatCurrency(overview.total_value) }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-cyan-500/20 bg-cyan-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            En revision
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-cyan-700 dark:text-cyan-200"
                        >
                            {{ overview.in_review }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-blue-500/20 bg-blue-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Vencen hoy
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-blue-700 dark:text-blue-200"
                        >
                            {{ overview.due_today }}
                        </p>
                    </article>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-for="view in views"
                        :key="view.key"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition-colors"
                        :class="
                            currentView === view.key
                                ? 'bg-primary text-primary-foreground shadow-sm'
                                : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'
                        "
                        @click="currentView = view.key"
                    >
                        <span>{{ view.label }}</span>
                        <span
                            class="rounded-full bg-black/10 px-2 py-0.5 text-[11px] font-bold text-current"
                        >
                            {{ view.count }}
                        </span>
                    </button>
                </div>
            </div>
        </section>

        <ProjectsTable
            :items="projects.data"
            :links="projects.links"
            :from="pageFrom"
            :to="pageTo"
            :total="totalProjects"
            @delete="handleDelete"
        />
    </div>
</template>
