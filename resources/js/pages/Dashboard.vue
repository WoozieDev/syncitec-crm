<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    BriefcaseBusiness,
    ClipboardList,
    ListTodo,
    UserRound,
    WalletCards,
    Wrench,
} from 'lucide-vue-next';
import { dashboard } from '@/routes';
import { index as projectsIndex } from '@/routes/projects';
import { index as servicesIndex } from '@/routes/services';

interface DashboardProps {
    summary: {
        total_clients: number;
        total_projects: number;
        projects_active: number;
        projects_in_review: number;
        projects_completed: number;
        total_services: number;
        services_active: number;
    };
    financial: {
        projects_billed: number;
        projects_collected: number;
        projects_pending: number;
        services_billed: number;
        services_collected: number;
        services_pending: number;
    };
    projects: Array<{
        id: number;
        name: string;
        status: string;
        status_label: string;
        price: number;
        due_date: string | null;
        client: string;
    }>;
    services: Array<{
        id: number;
        name: string;
        status: string;
        status_label: string;
        next_renewal_date: string | null;
        client: string;
    }>;
    tasks: {
        pending: number;
        in_progress: number;
        completed: number;
        priority_items: Array<{
            id: number;
            title: string;
            priority: string | null;
            source: string;
            project: string | null;
            created_at: string | null;
        }>;
    };
    alerts: {
        projects_due_soon: Array<{
            id: number;
            name: string;
            date: string | null;
            client: string;
        }>;
        services_renewing_soon: Array<{
            id: number;
            name: string;
            date: string | null;
            client: string;
        }>;
    };
}

const props = defineProps<DashboardProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const dateFormatter = new Intl.DateTimeFormat('es-PE', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
});

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;

const formatDate = (value: string | null): string => {
    if (!value) {
        return 'Sin fecha';
    }

    const date = new Date(`${value}T00:00:00`);

    return dateFormatter.format(date);
};
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"
            >
                <div class="space-y-3">
                    <p
                        class="text-xs font-bold tracking-[0.2em] text-muted-foreground uppercase"
                    >
                        Vista general del CRM
                    </p>
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Dashboard principal
                    </h1>
                    <p class="max-w-2xl text-sm text-muted-foreground">
                        Resumen operativo y financiero con informacion real de
                        clientes, proyectos, servicios y tareas.
                    </p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article
                class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <p
                        class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Clientes
                    </p>
                    <UserRound class="size-4 text-primary" />
                </div>
                <p class="mt-3 text-3xl font-black tracking-tight">
                    {{ props.summary.total_clients }}
                </p>
            </article>

            <article
                class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <p
                        class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Proyectos
                    </p>
                    <BriefcaseBusiness class="size-4 text-primary" />
                </div>
                <p class="mt-3 text-3xl font-black tracking-tight">
                    {{ props.summary.total_projects }}
                </p>
                <p class="mt-1 text-xs text-muted-foreground">
                    Activos: {{ props.summary.projects_active }} · En revision:
                    {{ props.summary.projects_in_review }}
                </p>
            </article>

            <article
                class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <p
                        class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Servicios
                    </p>
                    <Wrench class="size-4 text-primary" />
                </div>
                <p class="mt-3 text-3xl font-black tracking-tight">
                    {{ props.summary.total_services }}
                </p>
                <p class="mt-1 text-xs text-muted-foreground">
                    Activos: {{ props.summary.services_active }}
                </p>
            </article>

            <article
                class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <p
                        class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Tareas completadas
                    </p>
                    <ListTodo class="size-4 text-primary" />
                </div>
                <p class="mt-3 text-3xl font-black tracking-tight">
                    {{ props.tasks.completed }}
                </p>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <article
                    class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm"
                >
                    <div class="mb-5 flex items-center gap-2">
                        <WalletCards class="size-5 text-primary" />
                        <h2 class="text-xl font-bold tracking-tight">
                            Resumen financiero
                        </h2>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div
                            class="rounded-xl border border-border/60 bg-background/60 p-4"
                        >
                            <h3 class="text-sm font-semibold">Proyectos</h3>
                            <div class="mt-3 space-y-2 text-sm">
                                <p class="flex justify-between gap-4">
                                    <span class="text-muted-foreground"
                                        >Facturado</span
                                    ><strong>{{
                                        formatCurrency(
                                            props.financial.projects_billed,
                                        )
                                    }}</strong>
                                </p>
                                <p class="flex justify-between gap-4">
                                    <span class="text-muted-foreground"
                                        >Cobrado</span
                                    ><strong>{{
                                        formatCurrency(
                                            props.financial.projects_collected,
                                        )
                                    }}</strong>
                                </p>
                                <p class="flex justify-between gap-4">
                                    <span class="text-muted-foreground"
                                        >Pendiente</span
                                    ><strong>{{
                                        formatCurrency(
                                            props.financial.projects_pending,
                                        )
                                    }}</strong>
                                </p>
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-border/60 bg-background/60 p-4"
                        >
                            <h3 class="text-sm font-semibold">Servicios</h3>
                            <div class="mt-3 space-y-2 text-sm">
                                <p class="flex justify-between gap-4">
                                    <span class="text-muted-foreground"
                                        >Facturado</span
                                    ><strong>{{
                                        formatCurrency(
                                            props.financial.services_billed,
                                        )
                                    }}</strong>
                                </p>
                                <p class="flex justify-between gap-4">
                                    <span class="text-muted-foreground"
                                        >Cobrado</span
                                    ><strong>{{
                                        formatCurrency(
                                            props.financial.services_collected,
                                        )
                                    }}</strong>
                                </p>
                                <p class="flex justify-between gap-4">
                                    <span class="text-muted-foreground"
                                        >Pendiente</span
                                    ><strong>{{
                                        formatCurrency(
                                            props.financial.services_pending,
                                        )
                                    }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </article>

                <article
                    class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm"
                >
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h2 class="text-xl font-bold tracking-tight">
                            Proyectos recientes
                        </h2>
                        <Link
                            :href="projectsIndex()"
                            class="text-sm font-semibold text-primary hover:opacity-80"
                            >Ver todos</Link
                        >
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr
                                    class="text-xs font-semibold tracking-wide text-muted-foreground"
                                >
                                    <th class="pb-3">Proyecto</th>
                                    <th class="pb-3">Cliente</th>
                                    <th class="pb-3">Estado</th>
                                    <th class="pb-3">Precio</th>
                                    <th class="pb-3">Vence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="project in props.projects"
                                    :key="project.id"
                                    class="border-t border-border/50"
                                >
                                    <td class="py-3 font-medium">
                                        {{ project.name }}
                                    </td>
                                    <td class="py-3 text-muted-foreground">
                                        {{ project.client }}
                                    </td>
                                    <td class="py-3">
                                        {{ project.status_label }}
                                    </td>
                                    <td class="py-3">
                                        {{ formatCurrency(project.price) }}
                                    </td>
                                    <td class="py-3 text-muted-foreground">
                                        {{ formatDate(project.due_date) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <div class="space-y-6 xl:col-span-4">
                <article
                    class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold tracking-tight">
                            Servicios / renovaciones
                        </h2>
                        <Link
                            :href="servicesIndex()"
                            class="text-sm font-semibold text-primary hover:opacity-80"
                            >Ver todos</Link
                        >
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="service in props.services"
                            :key="service.id"
                            class="rounded-xl border border-border/60 bg-background/70 p-3"
                        >
                            <p class="text-sm font-semibold">
                                {{ service.name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ service.client }}
                            </p>
                            <div
                                class="mt-2 flex items-center justify-between text-xs"
                            >
                                <span>{{ service.status_label }}</span>
                                <span class="text-muted-foreground">{{
                                    formatDate(service.next_renewal_date)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </article>

                <article
                    class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm"
                >
                    <div class="mb-4 flex items-center gap-2">
                        <ClipboardList class="size-5 text-primary" />
                        <h2 class="text-lg font-bold tracking-tight">Tareas</h2>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="rounded-lg bg-muted p-2">
                            <p class="font-semibold">Pendientes</p>
                            <p class="mt-1 text-lg font-black">
                                {{ props.tasks.pending }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-muted p-2">
                            <p class="font-semibold">En progreso</p>
                            <p class="mt-1 text-lg font-black">
                                {{ props.tasks.in_progress }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-muted p-2">
                            <p class="font-semibold">Completadas</p>
                            <p class="mt-1 text-lg font-black">
                                {{ props.tasks.completed }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <p
                            class="text-xs font-semibold tracking-[0.14em] text-muted-foreground uppercase"
                        >
                            Prioridad alta
                        </p>
                        <div
                            v-for="task in props.tasks.priority_items"
                            :key="`${task.source}-${task.id}`"
                            class="rounded-xl border border-border/60 bg-background/70 p-3"
                        >
                            <p class="text-sm font-semibold">
                                {{ task.title }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{
                                    task.source === 'personal'
                                        ? 'Tarea personal'
                                        : `Proyecto: ${task.project ?? 'Sin proyecto'}`
                                }}
                            </p>
                        </div>
                    </div>
                </article>

                <article
                    class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm"
                >
                    <div class="mb-4 flex items-center gap-2">
                        <AlertTriangle class="size-5 text-amber-500" />
                        <h2 class="text-lg font-bold tracking-tight">
                            Alertas rapidas
                        </h2>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p
                                class="text-xs font-semibold tracking-[0.14em] text-muted-foreground uppercase"
                            >
                                Proyectos proximos a vencer
                            </p>
                            <ul class="mt-2 space-y-1">
                                <li
                                    v-for="item in props.alerts
                                        .projects_due_soon"
                                    :key="`project-alert-${item.id}`"
                                    class="flex items-center justify-between gap-3"
                                >
                                    <span class="truncate">{{
                                        item.name
                                    }}</span>
                                    <span
                                        class="text-xs text-muted-foreground"
                                        >{{ formatDate(item.date) }}</span
                                    >
                                </li>
                            </ul>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold tracking-[0.14em] text-muted-foreground uppercase"
                            >
                                Servicios proximos a renovar
                            </p>
                            <ul class="mt-2 space-y-1">
                                <li
                                    v-for="item in props.alerts
                                        .services_renewing_soon"
                                    :key="`service-alert-${item.id}`"
                                    class="flex items-center justify-between gap-3"
                                >
                                    <span class="truncate">{{
                                        item.name
                                    }}</span>
                                    <span
                                        class="text-xs text-muted-foreground"
                                        >{{ formatDate(item.date) }}</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
</template>
