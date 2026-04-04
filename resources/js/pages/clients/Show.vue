<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Building2,
    CalendarDays,
    Mail,
    MapPin,
    Pencil,
    Phone,
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
import type { ClientShowProps } from '@/modules/clients/types';
import ProjectStatusBadge from '@/modules/projects/components/ProjectStatusBadge.vue';
import { edit, index } from '@/routes/clients';
import { show as showProject } from '@/routes/projects';

defineProps<ClientShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes',
                href: index(),
            },
            {
                title: 'Detalle del cliente',
                href: '#',
            },
        ],
    },
});

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const serviceStatusClasses: Record<string, string> = {
    activo: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
    pendiente:
        'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200',
    suspendido:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    cancelado:
        'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
};

const paymentSourceClasses: Record<string, string> = {
    project:
        'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200',
    service:
        'bg-cyan-500/15 text-cyan-700 dark:bg-cyan-400/15 dark:text-cyan-200',
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

const formatDateTime = (value?: string | null): string => {
    const parsedDate = parseDate(value);

    if (!parsedDate) {
        return '-';
    }

    return parsedDate.toLocaleString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatLabel = (value: string | null | undefined): string => {
    if (!value) {
        return 'Sin dato';
    }

    return value.charAt(0).toUpperCase() + value.slice(1);
};

const formatServiceBilling = (
    billingType: string,
    billingCycle: string | null,
): string => {
    if (!billingCycle) {
        return formatLabel(billingType);
    }

    return `${formatLabel(billingType)} · ${formatLabel(billingCycle)}`;
};
</script>

<template>
    <Head :title="`Cliente - ${client.name}`" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="space-y-2">
                    <h1
                        class="text-3xl font-black tracking-tight text-foreground sm:text-4xl xl:text-[3.4rem] xl:leading-none"
                    >
                        {{ client.name }}
                    </h1>
                    <p
                        class="text-base font-medium text-muted-foreground sm:text-xl"
                    >
                        {{ textOrFallback(client.company, client.email) }}
                    </p>
                </div>

                <Link :href="edit(client.id)">
                    <Button
                        variant="outline"
                        class="w-full cursor-pointer sm:w-auto"
                    >
                        <Pencil class="size-4" />
                        <span>Editar cliente</span>
                    </Button>
                </Link>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_420px]">
            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Informacion del cliente</CardTitle>
                    <CardDescription>
                        Datos principales registrados actualmente para este
                        cliente.
                    </CardDescription>
                </CardHeader>

                <CardContent class="grid gap-6 md:grid-cols-2">
                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Nombre
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <UserRound class="size-4 text-muted-foreground" />
                            <p class="font-medium">{{ client.name }}</p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Empresa
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <Building2 class="size-4 text-muted-foreground" />
                            <p class="font-medium">
                                {{ textOrFallback(client.company) }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Correo
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <Mail class="size-4 text-muted-foreground" />
                            <p class="font-medium">{{ client.email }}</p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Telefono
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <Phone class="size-4 text-muted-foreground" />
                            <p class="font-medium">
                                {{ textOrFallback(client.phone) }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Pais
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <MapPin class="size-4 text-muted-foreground" />
                            <p class="font-medium">
                                {{ textOrFallback(client.country) }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Fecha de registro
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <CalendarDays
                                class="size-4 text-muted-foreground"
                            />
                            <p class="font-medium">
                                {{ formatDateTime(client.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Notas
                        </p>
                        <p
                            class="mt-2 text-sm whitespace-pre-line text-muted-foreground"
                        >
                            {{
                                client.notes?.trim() || 'Sin notas registradas.'
                            }}
                        </p>
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
                        <div>
                            <p
                                class="text-xs font-semibold tracking-[0.22em] text-white/65 uppercase"
                            >
                                Resumen financiero
                            </p>
                            <p class="mt-2 text-sm text-white/70">
                                Pagos reales registrados para este cliente.
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-white/70">Total cobrado</p>
                        <p class="mt-1 text-4xl font-black tracking-tight">
                            {{
                                formatCurrency(
                                    client.financial_summary.total_received,
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
                                Pagos proyectos
                            </p>
                            <p class="mt-2 text-2xl font-black text-cyan-200">
                                {{
                                    formatCurrency(
                                        client.financial_summary.project_paid,
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
                                Pagos servicios
                            </p>
                            <p class="mt-2 text-2xl font-black text-rose-200">
                                {{
                                    formatCurrency(
                                        client.financial_summary.service_paid,
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="space-y-3 border-t border-white/15 pt-4 text-sm"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Saldo proyectos</span>
                            <span class="font-bold text-white">
                                {{
                                    formatCurrency(
                                        client.financial_summary
                                            .project_balance,
                                    )
                                }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">
                                Valor servicios recurrentes
                            </span>
                            <span class="font-bold text-white">
                                {{
                                    formatCurrency(
                                        client.financial_summary
                                            .recurring_services_value,
                                    )
                                }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">
                                Cantidad servicios recurrentes
                            </span>
                            <span class="font-bold text-white">
                                {{
                                    client.financial_summary
                                        .recurring_services_count
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div
            class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]"
        >
            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Proyectos asociados</CardTitle>
                    <CardDescription>
                        Proyectos reales vinculados actualmente a este cliente.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <div v-if="client.projects.length > 0" class="space-y-4">
                        <article
                            v-for="project in client.projects"
                            :key="project.id"
                            class="rounded-2xl border border-border/60 bg-background/75 p-5 transition-colors hover:bg-accent/20"
                        >
                            <div
                                class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
                            >
                                <div class="space-y-3">
                                    <div class="space-y-1">
                                        <Link
                                            :href="showProject(project.id)"
                                            class="text-base font-bold text-foreground transition-colors hover:text-primary"
                                        >
                                            {{ project.name }}
                                        </Link>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ project.project_code }}
                                        </p>
                                    </div>

                                    <ProjectStatusBadge
                                        :label="project.status_label"
                                        :tone="project.status_tone"
                                    />
                                </div>

                                <div
                                    class="grid gap-4 sm:grid-cols-4 lg:min-w-130"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-semibold text-muted-foreground uppercase"
                                        >
                                            Total
                                        </p>
                                        <p
                                            class="mt-1 font-semibold text-foreground"
                                        >
                                            {{
                                                formatCurrency(
                                                    project.total_amount,
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold text-muted-foreground uppercase"
                                        >
                                            Pagado
                                        </p>
                                        <p
                                            class="mt-1 font-semibold text-emerald-600 dark:text-emerald-300"
                                        >
                                            {{
                                                formatCurrency(
                                                    project.paid_amount,
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold text-muted-foreground uppercase"
                                        >
                                            Saldo
                                        </p>
                                        <p
                                            class="mt-1 font-semibold text-foreground"
                                        >
                                            {{
                                                formatCurrency(
                                                    project.balance_due,
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold text-muted-foreground uppercase"
                                        >
                                            Entrega
                                        </p>
                                        <p
                                            class="mt-1 font-semibold text-foreground"
                                        >
                                            {{ formatDate(project.due_date) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div
                        v-else
                        class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-8 text-center"
                    >
                        <p class="text-sm text-muted-foreground">
                            Este cliente no tiene proyectos asociados por ahora.
                        </p>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Servicios recurrentes</CardTitle>
                    <CardDescription>
                        Servicios con facturacion recurrente registrados para el
                        cliente.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <div v-if="client.services.length > 0" class="space-y-4">
                        <article
                            v-for="service in client.services"
                            :key="service.id"
                            class="rounded-2xl border border-border/60 bg-background/75 p-5"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-2">
                                    <div>
                                        <p class="font-bold text-foreground">
                                            {{ service.name }}
                                        </p>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{
                                                textOrFallback(
                                                    service.service_type,
                                                    'Sin tipo',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.18em] uppercase"
                                        :class="
                                            serviceStatusClasses[
                                                service.status
                                            ] ?? serviceStatusClasses.pendiente
                                        "
                                    >
                                        {{ formatLabel(service.status) }}
                                    </span>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-muted-foreground">
                                        Precio
                                    </p>
                                    <p
                                        class="text-lg font-black text-foreground"
                                    >
                                        {{ formatCurrency(service.price) }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                                <div>
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Facturacion
                                    </p>
                                    <p class="mt-1 font-medium text-foreground">
                                        {{
                                            formatServiceBilling(
                                                service.billing_type,
                                                service.billing_cycle,
                                            )
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Proveedor
                                    </p>
                                    <p class="mt-1 font-medium text-foreground">
                                        {{ textOrFallback(service.provider) }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Proxima renovacion
                                    </p>
                                    <p class="mt-1 font-medium text-foreground">
                                        {{
                                            formatDate(
                                                service.next_renewal_date,
                                            )
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs text-muted-foreground uppercase"
                                    >
                                        Pagado
                                    </p>
                                    <p class="mt-1 font-medium text-foreground">
                                        {{
                                            formatCurrency(service.paid_amount)
                                        }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div
                        v-else
                        class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-8 text-center"
                    >
                        <p class="text-sm text-muted-foreground">
                            Este cliente no tiene servicios recurrentes
                            registrados.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card class="border-border/60">
            <CardHeader>
                <CardTitle>Pagos recientes</CardTitle>
                <CardDescription>
                    Ultimos pagos de proyectos y servicios registrados para este
                    cliente.
                </CardDescription>
            </CardHeader>

            <CardContent>
                <div v-if="client.recent_payments.length > 0" class="space-y-4">
                    <article
                        v-for="payment in client.recent_payments"
                        :key="payment.id"
                        class="flex flex-col gap-4 rounded-2xl border border-border/60 bg-background/75 p-5 lg:flex-row lg:items-start lg:justify-between"
                    >
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.18em] uppercase"
                                    :class="
                                        paymentSourceClasses[
                                            payment.source_type
                                        ] ?? paymentSourceClasses.project
                                    "
                                >
                                    {{ payment.source_label }}
                                </span>

                                <span class="text-xs text-muted-foreground">
                                    {{ formatDate(payment.payment_date) }}
                                </span>
                            </div>

                            <div>
                                <p class="font-bold text-foreground">
                                    {{ payment.source_name }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Metodo:
                                    {{ formatLabel(payment.payment_method) }}
                                </p>
                            </div>

                            <p
                                v-if="payment.notes"
                                class="text-sm text-muted-foreground"
                            >
                                {{ payment.notes }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 lg:text-right">
                            <div
                                class="inline-flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary"
                            >
                                <Wallet class="size-5" />
                            </div>

                            <div>
                                <p
                                    class="text-xs text-muted-foreground uppercase"
                                >
                                    Monto
                                </p>
                                <p class="text-xl font-black text-foreground">
                                    {{ formatCurrency(payment.amount) }}
                                </p>
                            </div>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-8 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        Este cliente no tiene pagos registrados por ahora.
                    </p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
