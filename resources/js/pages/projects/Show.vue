<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Building2,
    Eye,
    Mail,
    MapPin,
    Pencil,
    Phone,
    Plus,
    SquareKanban,
    UserRound,
    Wallet,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import ProjectStatusBadge from '@/modules/projects/components/ProjectStatusBadge.vue';
import type { ProjectShowProps } from '@/modules/projects/types';
import { show as showClient } from '@/routes/clients';
import { create as createProjectPayment } from '@/routes/project-payments';
import {
    create as createProjectTask,
    edit as editProjectTask,
    index as indexProjectTasks,
    show as showProjectTask,
} from '@/routes/project-tasks';
import { edit, index } from '@/routes/projects';

defineProps<ProjectShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Proyectos',
                href: index(),
            },
            {
                title: 'Detalle del proyecto',
                href: '#',
            },
        ],
    },
});

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const priorityClasses: Record<string, string> = {
    alta: 'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
    media: 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200',
    baja: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
};

const taskStatusClasses: Record<string, string> = {
    pendiente:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    en_progreso:
        'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200',
    en_revision:
        'bg-cyan-500/15 text-cyan-700 dark:bg-cyan-400/15 dark:text-cyan-200',
    completada:
        'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
};

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;

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
    <Head :title="`Proyecto - ${project.name}`" />

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
                        <ProjectStatusBadge
                            :label="project.status_label"
                            :tone="project.status_tone"
                        />

                        <span
                            class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-medium text-muted-foreground"
                        >
                            {{ project.client.display_name }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h1
                            class="text-3xl font-black tracking-tight text-foreground sm:text-4xl xl:text-[3.2rem] xl:leading-none"
                        >
                            {{ project.name }}
                        </h1>
                        <p
                            class="text-base font-medium text-muted-foreground sm:text-lg"
                        >
                            {{ project.project_code }}
                        </p>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                    <Link :href="edit(project.id)">
                        <Button
                            variant="outline"
                            class="w-full cursor-pointer sm:w-auto"
                        >
                            <Pencil class="size-4" />
                            <span>Editar proyecto</span>
                        </Button>
                    </Link>

                    <Link
                        :href="createProjectPayment({ query: { project: project.id } })"
                    >
                        <Button
                            variant="outline"
                            class="w-full cursor-pointer sm:w-auto"
                        >
                            <Wallet class="size-4" />
                            <span>Registrar pago</span>
                        </Button>
                    </Link>

                    <Link
                        :href="createProjectTask({ query: { project: project.id } })"
                    >
                        <Button class="w-full cursor-pointer sm:w-auto">
                            <Plus class="size-4" />
                            <span>Anadir tarea</span>
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.65fr)_minmax(0,1fr)]">
            <div class="space-y-6">
                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Project overview</CardTitle>
                        <CardDescription>
                            Datos reales del alcance, cronograma y notas del
                            proyecto.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-6">
                        <p
                            class="text-sm leading-relaxed text-muted-foreground"
                        >
                            {{
                                textOrFallback(
                                    project.description,
                                    'Sin descripcion registrada.',
                                )
                            }}
                        </p>

                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Fecha de inicio
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ formatDate(project.start_date) }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Fecha de entrega
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ formatDate(project.due_date) }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Precio total
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ formatCurrency(project.total_amount) }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl border border-border/60 bg-muted/25 p-4 text-sm text-muted-foreground"
                        >
                            {{
                                textOrFallback(
                                    project.notes,
                                    'Sin notas registradas.',
                                )
                            }}
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-border/60">
                    <CardHeader
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <CardTitle>Tareas del proyecto</CardTitle>
                            <CardDescription>
                                Seguimiento operativo en base a tareas
                                registradas.
                            </CardDescription>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                :href="indexProjectTasks({ query: { project_id: project.id } })"
                            >
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="cursor-pointer"
                                >
                                    Ver todas
                                </Button>
                            </Link>

                            <Link
                                :href="createProjectTask({ query: { project: project.id } })"
                            >
                                <Button size="sm" class="cursor-pointer">
                                    <Plus class="size-4" />
                                    <span>Anadir tarea</span>
                                </Button>
                            </Link>
                        </div>
                    </CardHeader>

                    <CardContent>
                        <div v-if="project.tasks.length > 0" class="space-y-3">
                            <article
                                v-for="task in project.tasks"
                                :key="task.id"
                                class="rounded-2xl border border-border/60 bg-background/80 p-4"
                            >
                                <div class="flex flex-col gap-4">
                                    <div
                                        class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                                    >
                                        <div class="space-y-1">
                                            <p
                                                class="font-semibold text-foreground"
                                            >
                                                {{ task.title }}
                                            </p>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                Modulo:
                                                {{
                                                    task.module?.name ??
                                                    'Sin modulo asociado'
                                                }}
                                            </p>
                                            <p
                                                class="text-sm text-muted-foreground"
                                            >
                                                {{
                                                    textOrFallback(
                                                        task.description,
                                                        'Sin descripcion.',
                                                    )
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="flex flex-wrap items-center gap-2"
                                        >
                                            <span
                                                class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-[11px] font-bold tracking-[0.12em] text-muted-foreground uppercase"
                                            >
                                                Orden {{ task.order }}
                                            </span>

                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.12em] uppercase"
                                                :class="
                                                    priorityClasses[
                                                        (
                                                            task.priority ?? ''
                                                        ).toLowerCase()
                                                    ] ?? priorityClasses.media
                                                "
                                            >
                                                {{
                                                    formatLabel(
                                                        task.priority ??
                                                            'sin prioridad',
                                                    )
                                                }}
                                            </span>

                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.12em] uppercase"
                                                :class="
                                                    taskStatusClasses[
                                                        task.status.toLowerCase()
                                                    ] ??
                                                    taskStatusClasses.pendiente
                                                "
                                            >
                                                {{ formatLabel(task.status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="showProjectTask(task.id)"
                                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                            title="Ver detalle"
                                        >
                                            <Eye class="size-4" />
                                        </Link>

                                        <Link
                                            :href="editProjectTask(task.id)"
                                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                            title="Editar"
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div
                            v-else
                            class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-8 text-center"
                        >
                            <p class="text-sm text-muted-foreground">
                                Este proyecto no tiene tareas registradas por
                                ahora.
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-border/60">
                    <CardHeader
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <CardTitle>Pagos del proyecto</CardTitle>
                            <CardDescription>
                                Historial real de pagos vinculados al proyecto.
                            </CardDescription>
                        </div>

                        <Link
                            :href="createProjectPayment({ query: { project: project.id } })"
                        >
                            <Button
                                variant="outline"
                                size="sm"
                                class="cursor-pointer"
                            >
                                <Plus class="size-4" />
                                <span>Registrar pago</span>
                            </Button>
                        </Link>
                    </CardHeader>

                    <CardContent>
                        <div
                            v-if="project.payments.length > 0"
                            class="space-y-3"
                        >
                            <article
                                v-for="payment in project.payments"
                                :key="payment.id"
                                class="grid gap-3 rounded-2xl border border-border/60 bg-background/80 p-4 sm:grid-cols-4 sm:items-center"
                            >
                                <div>
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Fecha
                                    </p>
                                    <p
                                        class="mt-1 text-sm font-semibold text-foreground"
                                    >
                                        {{ formatDate(payment.payment_date) }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Monto
                                    </p>
                                    <p
                                        class="mt-1 text-sm font-semibold text-primary"
                                    >
                                        {{ formatCurrency(payment.amount) }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Metodo
                                    </p>
                                    <p
                                        class="mt-1 text-sm font-semibold text-foreground"
                                    >
                                        {{
                                            formatLabel(payment.payment_method)
                                        }}
                                    </p>
                                </div>

                                <div class="sm:text-right">
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Notas
                                    </p>
                                    <p
                                        class="mt-1 text-sm text-muted-foreground"
                                    >
                                        {{
                                            textOrFallback(
                                                payment.notes,
                                                'Sin notas',
                                            )
                                        }}
                                    </p>
                                </div>
                            </article>
                        </div>

                        <div
                            v-else
                            class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-8 text-center"
                        >
                            <p class="text-sm text-muted-foreground">
                                Este proyecto no tiene pagos registrados por
                                ahora.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-6">
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
                                Financial overview
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-white/70">
                                Total del proyecto
                            </p>
                            <p class="mt-1 text-4xl font-black tracking-tight">
                                {{
                                    formatCurrency(
                                        project.financial_summary.total_price,
                                    )
                                }}
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div
                                class="rounded-2xl border border-white/10 bg-black/15 p-4"
                            >
                                <p
                                    class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                                >
                                    Pagado
                                </p>
                                <p
                                    class="mt-2 text-2xl font-black text-cyan-200"
                                >
                                    {{
                                        formatCurrency(
                                            project.financial_summary
                                                .total_paid,
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="rounded-2xl border border-white/10 bg-black/15 p-4"
                            >
                                <p
                                    class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                                >
                                    Pendiente
                                </p>
                                <p
                                    class="mt-2 text-2xl font-black text-rose-200"
                                >
                                    {{
                                        formatCurrency(
                                            project.financial_summary
                                                .pending_balance,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Perfil del cliente</CardTitle>
                        <CardDescription>
                            Contacto y empresa asociados al proyecto.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="inline-flex size-11 items-center justify-center rounded-xl bg-primary/10 text-primary"
                            >
                                <UserRound class="size-5" />
                            </div>
                            <div>
                                <p class="font-semibold text-foreground">
                                    {{ textOrFallback(project.client.name) }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ textOrFallback(project.client.company) }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-start gap-2">
                                <Building2
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(project.client.company) }}
                                </p>
                            </div>

                            <div class="flex items-start gap-2">
                                <Mail
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(project.client.email) }}
                                </p>
                            </div>

                            <div class="flex items-start gap-2">
                                <Phone
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(project.client.phone) }}
                                </p>
                            </div>

                            <div class="flex items-start gap-2">
                                <MapPin
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(project.client.country) }}
                                </p>
                            </div>
                        </div>

                        <div v-if="project.client.id">
                            <Link :href="showClient(project.client.id)">
                                <Button
                                    variant="outline"
                                    class="w-full cursor-pointer"
                                >
                                    Ver detalle del cliente
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Modulos del proyecto</CardTitle>
                        <CardDescription>
                            Roadmap ordenado por secuencia real.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <ol v-if="project.modules.length > 0" class="space-y-3">
                            <li
                                v-for="module in project.modules"
                                :key="module.id"
                                class="flex items-start gap-3 rounded-2xl border border-border/60 bg-background/80 p-4"
                            >
                                <span
                                    class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary"
                                >
                                    {{ module.order }}
                                </span>

                                <div class="flex items-center gap-2">
                                    <SquareKanban
                                        class="size-4 text-muted-foreground"
                                    />
                                    <p
                                        class="text-sm font-semibold text-foreground"
                                    >
                                        {{ module.name }}
                                    </p>
                                </div>
                            </li>
                        </ol>

                        <div
                            v-else
                            class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-8 text-center"
                        >
                            <p class="text-sm text-muted-foreground">
                                Este proyecto no tiene modulos registrados por
                                ahora.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
