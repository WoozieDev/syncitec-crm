<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Filter, Search, Upload } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { ProjectIndexProps } from '@/modules/projects';
import { create, destroy, edit, index } from '@/routes/projects';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

const props = defineProps<ProjectIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Projects',
                href: index(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');
const currentView = ref(props.filters.view ?? 'all');
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const statusClasses: Record<string, string> = {
    planning:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/16 dark:text-slate-200',
    in_progress:
        'bg-blue-500/15 text-blue-700 dark:bg-blue-400/18 dark:text-blue-200',
    in_review:
        'bg-cyan-500/15 text-cyan-700 dark:bg-cyan-400/18 dark:text-cyan-200',
    done: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/18 dark:text-emerald-200',
    canceled: 'bg-red-500/15 text-red-700 dark:bg-red-400/18 dark:text-red-200',
};

const progressBarClasses: Record<string, string> = {
    planning: 'bg-slate-400 dark:bg-slate-500',
    in_progress: 'bg-blue-500 dark:bg-blue-400',
    in_review: 'bg-cyan-500 dark:bg-cyan-400',
    done: 'bg-emerald-500 dark:bg-emerald-400',
    canceled: 'bg-red-500 dark:bg-red-400',
};

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const compactCurrencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    notation: 'compact',
    maximumFractionDigits: 1,
});

const currentViewData = computed(
    () =>
        props.views.find((item) => item.key === currentView.value) ??
        props.views[0],
);

const revenueGoal = computed(() =>
    Math.max(props.overview.total_value * 1.2, 50000),
);
const revenueProgress = computed(() => {
    if (revenueGoal.value <= 0) {
        return 0;
    }

    return Math.min(
        100,
        Math.round((props.overview.total_value / revenueGoal.value) * 100),
    );
});

const formatCurrency = (value: number): string =>
    currencyFormatter.format(value);

const formatCompactCurrency = (value: number): string =>
    compactCurrencyFormatter.format(value);

const applyFilters = () => {
    router.get(
        index(),
        {
            search: search.value || undefined,
            view: currentView.value !== 'all' ? currentView.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

watch(currentView, () => {
    applyFilters();
});

watch(search, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 250);
});

onBeforeUnmount(() => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});

const parsePaginationLabel = (label: string) =>
    label
        .replace('&laquo; Previous', 'Previous')
        .replace('Next &raquo;', 'Next')
        .replace('&amp;', '&');

const handleDelete = (projectId: number, projectName: string) => {
    if (!confirm(`Are you sure you want to delete "${projectName}"?`)) {
        return;
    }

    router.delete(destroy(projectId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Projects" />

    <div
        class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 sm:p-6"
    >
        <section
            class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
        >
            <div class="relative w-full lg:max-w-lg">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    type="text"
                    placeholder="Search projects..."
                    class="h-11 rounded-full border-transparent bg-muted/80 pr-4 pl-10"
                />
            </div>

            <Link :href="create()">
                <Button class="h-11 rounded-full px-5 font-semibold">
                    + New Project
                </Button>
            </Link>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1fr_auto] xl:items-end">
            <div>
                <p
                    class="text-xs font-bold tracking-[0.2em] text-muted-foreground uppercase"
                >
                    Workspace Overview
                </p>
                <h1 class="text-4xl font-black tracking-tight">
                    Active Projects
                </h1>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <article
                    class="rounded-2xl border border-border/60 bg-card px-5 py-4 shadow-sm dark:bg-[#0c1428]"
                >
                    <p class="text-xs font-semibold text-muted-foreground">
                        Total Value
                    </p>
                    <p class="mt-1 text-3xl font-black tracking-tight">
                        {{ formatCurrency(overview.total_value) }}
                    </p>
                </article>

                <article
                    class="rounded-2xl border border-border/60 bg-card px-5 py-4 shadow-sm dark:bg-[#0c1428]"
                >
                    <p class="text-xs font-semibold text-muted-foreground">
                        In Review
                    </p>
                    <p
                        class="mt-1 text-3xl font-black tracking-tight text-cyan-600 dark:text-cyan-300"
                    >
                        {{ overview.in_review }}
                    </p>
                </article>

                <article
                    class="rounded-2xl border border-blue-500/20 bg-blue-500/10 px-5 py-4 shadow-sm dark:bg-blue-500/18"
                >
                    <p class="text-xs font-semibold text-muted-foreground">
                        Due Today
                    </p>
                    <p
                        class="mt-1 text-3xl font-black tracking-tight text-blue-700 dark:text-blue-200"
                    >
                        {{ overview.due_today }}
                    </p>
                </article>
            </div>
        </section>

        <section
            class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between"
        >
            <div
                class="flex flex-wrap items-center gap-2 rounded-2xl border border-border/60 bg-card p-2 dark:bg-[#0c1428]"
            >
                <button
                    v-for="view in views"
                    :key="view.key"
                    type="button"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition-colors"
                    :class="
                        currentView === view.key
                            ? 'bg-primary text-primary-foreground shadow-sm'
                            : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                    "
                    @click="currentView = view.key"
                >
                    {{ view.label }}
                </button>
            </div>

            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    class="h-10 rounded-full px-4 text-sm"
                >
                    <Filter class="mr-2 size-4" />
                    Advanced Filters
                </Button>
                <Button
                    variant="outline"
                    class="h-10 rounded-full px-4 text-sm"
                >
                    <Upload class="mr-2 size-4" />
                    Export
                </Button>
            </div>
        </section>

        <section
            class="overflow-hidden rounded-2xl border border-border/60 bg-card shadow-sm dark:border-white/10 dark:bg-[#0b1326]"
        >
            <Table>
                <TableHeader>
                    <TableRow class="bg-muted/70 dark:bg-white/5">
                        <TableHead>Project Name</TableHead>
                        <TableHead>Customer</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Progress</TableHead>
                        <TableHead class="text-right">Total Amount</TableHead>
                        <TableHead class="text-right">Balance Due</TableHead>
                        <TableHead class="w-42.5 text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow
                        v-for="project in projects.data"
                        :key="project.id"
                    >
                        <TableCell>
                            <div class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 h-11 w-1 rounded-full"
                                    :class="
                                        progressBarClasses[
                                            project.status_tone
                                        ] ?? progressBarClasses.planning
                                    "
                                />

                                <div>
                                    <p class="text-sm font-semibold">
                                        {{ project.name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        ID: {{ project.project_code }}
                                    </p>
                                </div>
                            </div>
                        </TableCell>

                        <TableCell>
                            <p class="text-sm font-semibold">
                                {{ project.client.display_name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ project.client.name }}
                            </p>
                        </TableCell>

                        <TableCell>
                            <span
                                class="inline-flex rounded-md px-2 py-1 text-[11px] font-bold tracking-wide uppercase"
                                :class="
                                    statusClasses[project.status_tone] ??
                                    statusClasses.planning
                                "
                            >
                                {{ project.status_label }}
                            </span>
                        </TableCell>

                        <TableCell>
                            <div class="space-y-2">
                                <div
                                    class="flex items-center justify-between text-xs font-semibold"
                                >
                                    <span>{{ project.progress_percent }}%</span>
                                    <span class="text-muted-foreground">
                                        {{ project.progress_label }}
                                    </span>
                                </div>
                                <div class="h-2 w-44 rounded-full bg-muted">
                                    <div
                                        class="h-2 rounded-full transition-[width]"
                                        :class="
                                            progressBarClasses[
                                                project.status_tone
                                            ] ?? progressBarClasses.planning
                                        "
                                        :style="{
                                            width: `${project.progress_percent}%`,
                                        }"
                                    />
                                </div>
                            </div>
                        </TableCell>

                        <TableCell class="text-right text-sm font-semibold">
                            {{ formatCurrency(project.total_amount) }}
                        </TableCell>

                        <TableCell
                            class="text-right text-sm font-semibold"
                            :class="
                                project.balance_due === 0
                                    ? 'text-emerald-600 dark:text-emerald-300'
                                    : ''
                            "
                        >
                            {{ formatCurrency(project.balance_due) }}
                        </TableCell>

                        <TableCell class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <Link :href="edit(project.id)">
                                    <Button variant="outline" size="sm"
                                        >Edit</Button
                                    >
                                </Link>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    @click="
                                        handleDelete(project.id, project.name)
                                    "
                                >
                                    Delete
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="projects.data.length === 0">
                        <TableCell
                            colspan="7"
                            class="py-12 text-center text-sm text-muted-foreground"
                        >
                            No projects found with current filters.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <div
                class="flex flex-wrap items-center justify-between gap-3 border-t border-border/60 px-5 py-4"
            >
                <p class="text-sm text-muted-foreground">
                    Showing {{ projects.data.length }} of
                    {{ currentViewData?.count ?? 0 }} projects
                </p>

                <div
                    v-if="projects.links.length > 3"
                    class="flex items-center gap-2"
                >
                    <template
                        v-for="(link, indexLink) in projects.links"
                        :key="`${link.label}-${indexLink}`"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-md border px-3 py-2 text-sm transition hover:bg-muted"
                            :class="{
                                'bg-primary text-primary-foreground':
                                    link.active,
                            }"
                        >
                            {{ parsePaginationLabel(link.label) }}
                        </Link>

                        <span
                            v-else
                            class="rounded-md border px-3 py-2 text-sm text-muted-foreground opacity-50"
                        >
                            {{ parsePaginationLabel(link.label) }}
                        </span>
                    </template>
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-3">
            <article
                class="rounded-3xl bg-linear-to-r from-blue-600 to-blue-500 p-6 text-white shadow-xl shadow-blue-500/20 xl:col-span-2"
            >
                <h2 class="text-3xl font-black tracking-tight">
                    Quarterly Revenue Target
                </h2>
                <p class="mt-2 text-sm text-blue-100">
                    Current progress is {{ revenueProgress }}%. Completing
                    in-review work should move this up fast.
                </p>

                <div class="mt-8 flex items-center gap-4">
                    <div class="h-3 flex-1 rounded-full bg-white/35">
                        <div
                            class="h-3 rounded-full bg-white"
                            :style="{ width: `${revenueProgress}%` }"
                        />
                    </div>
                    <p class="text-4xl font-black">
                        {{ formatCompactCurrency(revenueGoal) }}
                    </p>
                </div>
            </article>

            <article
                class="rounded-3xl border border-border/60 bg-card p-6 shadow-sm dark:bg-[#121a2e]"
            >
                <p
                    class="text-xs font-bold tracking-[0.18em] text-primary uppercase"
                >
                    AI Recommendation
                </p>
                <h3 class="mt-3 text-2xl font-bold">Action Plan</h3>
                <p class="mt-3 text-sm text-muted-foreground">
                    {{ overview.in_review }} projects are currently in review
                    and {{ overview.due_today }} are due today. Consider
                    reallocating design and QA resources to clear bottlenecks.
                </p>
                <button
                    type="button"
                    class="mt-6 text-sm font-semibold text-primary transition hover:underline"
                >
                    View Action Plan ->
                </button>
            </article>
        </section>
    </div>
</template>
