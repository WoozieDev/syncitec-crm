<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CalendarDays,
    CreditCard,
    Pencil,
    ReceiptText,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import ServiceStatusBadge from '@/modules/services/components/ServiceStatusBadge.vue';
import type { ServicePaymentShowProps } from '@/modules/servicePayments/types';
import { show as showClient } from '@/routes/clients';
import { edit, index } from '@/routes/service-payments';
import { show as showService } from '@/routes/services';

defineProps<ServicePaymentShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pagos de servicios',
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
    <Head :title="`Pago de servicio #${payment.id}`" />

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
                        <Button variant="outline" class="w-full cursor-pointer sm:w-auto">
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

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(0,1fr)]">
            <div class="space-y-6">
                <Card class="overflow-hidden rounded-3xl border-border/60">
                    <CardContent class="p-0">
                        <section
                            class="relative overflow-hidden bg-linear-to-br from-background via-background to-primary/5 px-6 py-8 sm:px-8"
                        >
                            <div
                                class="absolute top-0 right-0 h-48 w-48 translate-x-1/3 -translate-y-1/3 rounded-full bg-primary/10 blur-3xl"
                            />

                            <div class="relative space-y-8">
                                <div
                                    class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                                >
                                    <div>
                                        <p class="text-xs font-bold tracking-[0.18em] text-muted-foreground uppercase">
                                            Monto transaccion
                                        </p>
                                        <p class="mt-2 text-5xl font-black tracking-tight text-primary">
                                            {{ formatCurrency(payment.amount) }}
                                        </p>
                                    </div>

                                    <div class="space-y-2 md:text-right">
                                        <p class="text-xs font-semibold text-muted-foreground">
                                            Registro
                                        </p>
                                        <p class="text-sm font-semibold text-foreground">
                                            #{{ payment.id }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid gap-5 md:grid-cols-3">
                                    <div>
                                        <p class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                                            Fecha de pago
                                        </p>
                                        <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-foreground">
                                            <CalendarDays class="size-4 text-primary" />
                                            {{ formatDate(payment.payment_date) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                                            Metodo de pago
                                        </p>
                                        <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-foreground">
                                            <CreditCard class="size-4 text-primary" />
                                            {{ textOrFallback(payment.payment_method) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                                            Fecha de registro
                                        </p>
                                        <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-foreground">
                                            <ReceiptText class="size-4 text-primary" />
                                            {{ formatDateTime(payment.created_at) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </CardContent>
                </Card>

                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Notas del pago</CardTitle>
                        <CardDescription>
                            Contexto registrado al momento de guardar la transaccion.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <div
                            class="rounded-2xl border border-border/60 bg-muted/25 p-4 text-sm leading-relaxed text-muted-foreground"
                        >
                            {{ textOrFallback(payment.notes, 'Sin notas registradas.') }}
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Relacion del pago</CardTitle>
                        <CardDescription>
                            Servicio, cliente, tipo y proveedor vinculados al pago.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                                Servicio
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ payment.service?.name ?? 'Sin servicio' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                                Cliente
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ payment.service?.client.display_name ?? 'Sin cliente' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                                Tipo de servicio
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ payment.service?.service_type.name ?? 'Sin tipo' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                                Proveedor
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ payment.service?.provider?.name ?? 'Sin proveedor' }}
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
                            <p class="text-xs font-semibold tracking-[0.22em] text-white/65 uppercase">
                                Servicio asociado
                            </p>
                        </div>

                        <div v-if="payment.service">
                            <p class="text-2xl font-black tracking-tight">
                                {{ payment.service.name }}
                            </p>
                            <p class="mt-1 text-sm text-white/70">
                                {{ payment.service.client.display_name }}
                            </p>
                        </div>

                        <div v-if="payment.service" class="flex items-center gap-2">
                            <ServiceStatusBadge
                                :label="payment.service.status_label ?? 'Sin estado'"
                                :tone="payment.service.status_tone ?? 'pending'"
                            />
                        </div>

                        <div v-if="payment.service" class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-black/15 p-4">
                                <p class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase">
                                    Precio servicio
                                </p>
                                <p class="mt-2 text-xl font-black text-cyan-200">
                                    {{ formatCurrency(payment.service.price ?? 0) }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-black/15 p-4">
                                <p class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase">
                                    Saldo estimado
                                </p>
                                <p class="mt-2 text-xl font-black text-rose-200">
                                    {{ formatCurrency(payment.service.balance_due ?? 0) }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="payment.service"
                            class="space-y-2 rounded-2xl border border-white/10 bg-black/15 p-4 text-sm"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-white/65">Tipo</span>
                                <span class="font-semibold text-white">
                                    {{ payment.service.service_type.name ?? 'Sin tipo' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-white/65">Proveedor</span>
                                <span class="font-semibold text-white">
                                    {{ payment.service.provider?.name ?? 'Sin proveedor' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-white/65">Proxima renovacion</span>
                                <span class="font-semibold text-white">
                                    {{ formatDate(payment.service.next_renewal_date) }}
                                </span>
                            </div>
                        </div>

                        <div v-if="payment.service" class="space-y-3">
                            <Link :href="showService(payment.service.id)">
                                <Button
                                    variant="outline"
                                    class="w-full cursor-pointer border-white/30 bg-transparent text-white hover:bg-white/10 hover:text-white"
                                >
                                    Ver detalle del servicio
                                </Button>
                            </Link>

                            <Link
                                v-if="payment.service.client.id"
                                :href="showClient(payment.service.client.id)"
                            >
                                <Button
                                    variant="outline"
                                    class="w-full cursor-pointer border-white/20 bg-white/5 text-white hover:bg-white/10 hover:text-white"
                                >
                                    Ver cliente asociado
                                </Button>
                            </Link>
                        </div>

                        <div
                            v-else
                            class="rounded-2xl border border-white/20 bg-white/10 p-5 text-sm text-white/75"
                        >
                            No hay servicio asociado disponible para este pago.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
