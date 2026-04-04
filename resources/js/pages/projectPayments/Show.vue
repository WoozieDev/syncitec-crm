<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CalendarDays, CreditCard, Pencil } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { ProjectPaymentShowProps } from '@/modules/projectPayments/types';
import { show as showProject } from '@/routes/projects';
import { edit, index } from '@/routes/project-payments';

defineProps<ProjectPaymentShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pagos',
                href: index(),
            },
            {
                title: 'Detalle del pago',
                href: '#',
            },
        ],
    },
});

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;

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

const textOrFallback = (
    value: string | null | undefined,
    fallback = 'Sin dato',
): string => {
    if (!value || value.trim().length === 0) {
        return fallback;
    }

    return value;
};
</script>

<template>
    <Head :title="`Pago #${payment.id}`" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div
                class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between"
            >
                <div class="space-y-2">
                    <p class="text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase">
                        Pago registrado
                    </p>
                    <h1
                        class="text-3xl font-black tracking-tight text-foreground sm:text-4xl"
                    >
                        {{ formatCurrency(payment.amount) }}
                    </h1>
                    <p class="text-sm text-muted-foreground">Pago #{{ payment.id }}</p>
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

                    <Link :href="edit(payment.id)">
                        <Button class="w-full cursor-pointer sm:w-auto">
                            <Pencil class="size-4" />
                            <span>Editar pago</span>
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Informacion general</CardTitle>
                    <CardDescription>
                        Datos reales del pago registrado.
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-6">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                            >
                                Monto
                            </p>
                            <p class="mt-1 text-2xl font-black text-primary">
                                {{ formatCurrency(payment.amount) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                            >
                                Fecha de pago
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ formatDate(payment.payment_date) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                            >
                                Metodo de pago
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ textOrFallback(payment.payment_method) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                            >
                                Fecha de registro
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ formatDate(payment.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-border/60 bg-muted/25 p-4 text-sm text-muted-foreground"
                    >
                        {{ textOrFallback(payment.notes, 'Sin notas registradas.') }}
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
                            Contexto del proyecto
                        </p>
                    </div>

                    <div v-if="payment.project">
                        <p class="text-sm text-white/70">Proyecto</p>
                        <p class="mt-1 text-2xl font-black tracking-tight">
                            {{ payment.project.name }}
                        </p>
                        <p class="mt-1 text-sm text-white/70">
                            {{ payment.project.client.display_name }}
                        </p>
                    </div>

                    <div v-if="payment.project" class="grid gap-3 sm:grid-cols-2">
                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <p
                                class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                            >
                                Total proyecto
                            </p>
                            <p class="mt-2 text-xl font-black text-cyan-200">
                                {{ formatCurrency(payment.project.total_amount ?? 0) }}
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <p
                                class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                            >
                                Saldo pendiente
                            </p>
                            <p class="mt-2 text-xl font-black text-rose-200">
                                {{ formatCurrency(payment.project.balance_due ?? 0) }}
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="payment.project"
                        class="space-y-2 rounded-2xl border border-white/10 bg-black/15 p-4 text-sm"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Estado</span>
                            <span class="font-semibold text-white">
                                {{ formatLabel(payment.project.status) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Pagado</span>
                            <span class="font-semibold text-white">
                                {{ formatCurrency(payment.project.paid_amount ?? 0) }}
                            </span>
                        </div>
                    </div>

                    <div v-if="payment.project">
                        <Link :href="showProject(payment.project.id)">
                            <Button
                                variant="outline"
                                class="w-full cursor-pointer border-white/30 bg-transparent text-white hover:bg-white/10 hover:text-white"
                            >
                                Ver detalle del proyecto
                            </Button>
                        </Link>
                    </div>

                    <div
                        v-else
                        class="rounded-2xl border border-white/20 bg-white/10 p-5 text-sm text-white/75"
                    >
                        No hay proyecto asociado disponible para este pago.
                    </div>
                </div>
            </section>
        </div>

        <Card class="border-border/60">
            <CardHeader>
                <CardTitle>Relacion del pago</CardTitle>
                <CardDescription>
                    Referencias de proyecto y cliente asociadas al pago.
                </CardDescription>
            </CardHeader>

            <CardContent class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Proyecto
                    </p>
                    <p class="mt-1 text-sm font-semibold text-foreground">
                        {{ payment.project?.name ?? 'Sin proyecto' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Cliente
                    </p>
                    <p class="mt-1 text-sm font-semibold text-foreground">
                        {{ payment.project?.client.display_name ?? 'Sin cliente' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Metodo
                    </p>
                    <p class="mt-1 flex items-center gap-2 text-sm font-semibold text-foreground">
                        <CreditCard class="size-4 text-muted-foreground" />
                        {{ textOrFallback(payment.payment_method) }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Fecha
                    </p>
                    <p class="mt-1 flex items-center gap-2 text-sm font-semibold text-foreground">
                        <CalendarDays class="size-4 text-muted-foreground" />
                        {{ formatDate(payment.payment_date) }}
                    </p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
