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
    Wrench,
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
import type { ServiceShowProps } from '@/modules/services/types';
import { show as showClient } from '@/routes/clients';
import { create as createServicePayment } from '@/routes/service-payments';
import { edit, index } from '@/routes/services';

defineProps<ServiceShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Servicios',
                href: index(),
            },
            {
                title: 'Detalle del servicio',
                href: '#',
            },
        ],
    },
});

const renewalToneClasses: Record<string, string> = {
    expired: 'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
    expiring:
        'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200',
    scheduled:
        'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200',
    none: 'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
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
</script>

<template>
    <Head :title="`Servicio - ${service.name}`" />

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
                        <ServiceStatusBadge
                            :label="service.status_label"
                            :tone="service.status_tone"
                        />

                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.12em] uppercase"
                            :class="
                                renewalToneClasses[service.renewal_state] ??
                                renewalToneClasses.scheduled
                            "
                        >
                            {{ service.renewal_label }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h1
                            class="text-3xl font-black tracking-tight text-foreground sm:text-4xl xl:text-[3.2rem] xl:leading-none"
                        >
                            {{ service.name }}
                        </h1>
                        <div
                            class="flex flex-wrap items-center gap-3 text-sm text-muted-foreground"
                        >
                            <span>{{ service.client.display_name }}</span>
                            <span>-</span>
                            <span>
                                {{
                                    textOrFallback(
                                        service.service_type.name,
                                        'Sin tipo',
                                    )
                                }}
                            </span>
                            <span>-</span>
                            <span>
                                {{
                                    textOrFallback(
                                        service.provider?.name,
                                        'Sin proveedor',
                                    )
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                    <Link :href="index()">
                        <Button
                            variant="outline"
                            class="w-full cursor-pointer sm:w-auto"
                        >
                            Volver
                        </Button>
                    </Link>

                    <Link :href="edit(service.id)">
                        <Button class="w-full cursor-pointer sm:w-auto">
                            <Pencil class="size-4" />
                            <span>Editar servicio</span>
                        </Button>
                    </Link>

                    <Link
                        :href="
                            createServicePayment({
                                query: { service: service.id },
                            })
                        "
                    >
                        <Button
                            variant="outline"
                            class="w-full cursor-pointer sm:w-auto"
                        >
                            Registrar pago
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(0,1fr)]">
            <div class="space-y-6">
                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Informacion del servicio</CardTitle>
                        <CardDescription>
                            Datos operativos y comerciales registrados para este
                            servicio.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-6">
                        <p class="text-sm leading-relaxed text-muted-foreground">
                            {{
                                textOrFallback(
                                    service.description,
                                    'Sin descripcion registrada.',
                                )
                            }}
                        </p>

                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Tipo de facturacion
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ service.billing_type_label }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Ciclo
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{
                                        textOrFallback(
                                            service.billing_cycle_label,
                                            'No aplica',
                                        )
                                    }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Estado
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ service.status_label }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Fecha de inicio
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ formatDate(service.start_date) }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Proxima renovacion
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ formatDate(service.next_renewal_date) }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                                >
                                    Ultima actualizacion
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{ formatDateTime(service.updated_at) }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl border border-border/60 bg-muted/25 p-4 text-sm text-muted-foreground"
                        >
                            {{
                                textOrFallback(
                                    service.notes,
                                    'Sin notas registradas.',
                                )
                            }}
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Contacto asociado</CardTitle>
                        <CardDescription>
                            Cliente relacionado con este servicio.
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
                                    {{ textOrFallback(service.client.name) }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ textOrFallback(service.client.company) }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-start gap-2">
                                <Building2
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(service.client.company) }}
                                </p>
                            </div>

                            <div class="flex items-start gap-2">
                                <Mail
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(service.client.email) }}
                                </p>
                            </div>

                            <div class="flex items-start gap-2">
                                <Phone
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(service.client.phone) }}
                                </p>
                            </div>

                            <div class="flex items-start gap-2">
                                <MapPin
                                    class="mt-0.5 size-4 text-muted-foreground"
                                />
                                <p class="text-sm text-foreground">
                                    {{ textOrFallback(service.client.country) }}
                                </p>
                            </div>
                        </div>

                        <div v-if="service.client.id">
                            <Link :href="showClient(service.client.id)">
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
                        <div class="flex items-center gap-3">
                            <div
                                class="inline-flex size-11 items-center justify-center rounded-2xl bg-white/10"
                            >
                                <Wallet class="size-5" />
                            </div>
                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-[0.2em] text-white/65 uppercase"
                                >
                                    Resumen economico
                                </p>
                                <p class="text-sm text-white/80">
                                    Servicio recurrente
                                </p>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-white/70">Precio</p>
                            <p class="mt-1 text-4xl font-black tracking-tight">
                                {{ formatCurrency(service.price) }}
                            </p>
                            <p class="mt-2 text-sm text-white/70">
                                {{ service.billing_type_label }}
                                <span v-if="service.billing_cycle_label">
                                    / {{ service.billing_cycle_label }}
                                </span>
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div
                                class="rounded-2xl border border-white/10 bg-black/15 p-4"
                            >
                                <p
                                    class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                                >
                                    Costo
                                </p>
                                <p class="mt-2 text-2xl font-black text-cyan-200">
                                    {{
                                        service.cost !== null
                                            ? formatCurrency(service.cost)
                                            : 'No definido'
                                    }}
                                </p>
                            </div>

                            <div
                                class="rounded-2xl border border-white/10 bg-black/15 p-4"
                            >
                                <p
                                    class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                                >
                                    Margen
                                </p>
                                <p class="mt-2 text-2xl font-black text-rose-200">
                                    {{
                                        service.cost !== null
                                            ? formatCurrency(service.margin_amount)
                                            : 'No definido'
                                    }}
                                </p>
                                <p class="mt-1 text-xs text-white/60">
                                    {{
                                        service.margin_percentage !== null
                                            ? `${service.margin_percentage.toFixed(1)}%`
                                            : 'Sin costo cargado'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-white/15 pt-4">
                            <div class="flex items-start gap-4">
                                <div
                                    class="rounded-2xl bg-white/10 p-3 text-white"
                                >
                                    <CalendarDays class="size-5" />
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] font-semibold tracking-[0.18em] text-white/55 uppercase"
                                    >
                                        Proxima renovacion
                                    </p>
                                    <p class="mt-1 text-lg font-black">
                                        {{ formatDate(service.next_renewal_date) }}
                                    </p>
                                    <p class="mt-1 text-xs text-white/70">
                                        {{ service.renewal_label }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <Card class="border-border/60">
                    <CardHeader>
                        <CardTitle>Ficha tecnica</CardTitle>
                        <CardDescription>
                            Datos base para futuras renovaciones y pagos del
                            servicio.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="inline-flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary"
                            >
                                <Wrench class="size-5" />
                            </div>
                            <div>
                                <p class="font-semibold text-foreground">
                                    {{ service.name }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{
                                        textOrFallback(
                                            service.service_type.name,
                                            'Sin tipo',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 text-sm sm:grid-cols-2">
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">
                                    Tipo
                                </p>
                                <p class="mt-1 font-medium text-foreground">
                                    {{ service.billing_type_label }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-muted-foreground uppercase">
                                    Ciclo
                                </p>
                                <p class="mt-1 font-medium text-foreground">
                                    {{
                                        textOrFallback(
                                            service.billing_cycle_label,
                                            'No aplica',
                                        )
                                    }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-muted-foreground uppercase">
                                    Creado
                                </p>
                                <p class="mt-1 font-medium text-foreground">
                                    {{ formatDateTime(service.created_at) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-muted-foreground uppercase">
                                    Actualizado
                                </p>
                                <p class="mt-1 font-medium text-foreground">
                                    {{ formatDateTime(service.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
