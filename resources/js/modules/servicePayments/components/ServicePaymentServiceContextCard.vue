<script setup lang="ts">
import { computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ServiceStatusBadge from '@/modules/services/components/ServiceStatusBadge.vue';
import type { ServicePaymentServiceOption } from '@/modules/servicePayments/types';

const props = defineProps<{
    services: ServicePaymentServiceOption[];
    selectedServiceId: string;
}>();

const selectedService = computed(() =>
    props.services.find((service) => String(service.id) === props.selectedServiceId),
);

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

    const parsedDate = new Date(`${value}T00:00:00`);

    return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
};

const formatDate = (value?: string | null): string => {
    const parsedDate = parseDate(value);

    if (!parsedDate) {
        return 'Sin fecha';
    }

    return parsedDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
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
    <Card class="h-fit overflow-hidden rounded-3xl border-border/60">
        <CardHeader class="pb-4">
            <CardTitle class="text-base">Contexto del servicio</CardTitle>
        </CardHeader>

        <CardContent>
            <div v-if="selectedService" class="space-y-5">
                <div
                    class="rounded-3xl border border-blue-400/20 bg-linear-to-br from-blue-600 via-blue-700 to-slate-950 p-5 text-white shadow-lg shadow-blue-950/20"
                >
                    <p
                        class="text-[11px] font-semibold tracking-[0.18em] text-white/60 uppercase"
                    >
                        Servicio asociado
                    </p>
                    <p class="mt-2 text-xl font-black leading-tight">
                        {{ selectedService.name }}
                    </p>
                    <p class="mt-1 text-sm text-white/70">
                        {{ selectedService.client.display_name }}
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <p
                            class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            Tipo de servicio
                        </p>
                        <p class="mt-1 text-sm font-semibold text-foreground">
                            {{
                                textOrFallback(
                                    selectedService.service_type.name,
                                    'Sin tipo',
                                )
                            }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            Proveedor
                        </p>
                        <p class="mt-1 text-sm font-semibold text-foreground">
                            {{
                                textOrFallback(
                                    selectedService.provider?.name,
                                    'Sin proveedor',
                                )
                            }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <ServiceStatusBadge
                            :label="selectedService.status_label"
                            :tone="selectedService.status_tone"
                        />
                    </div>

                    <div
                        class="grid gap-3 rounded-2xl border border-border/60 bg-muted/25 p-4"
                    >
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Precio</span>
                            <span class="font-semibold text-foreground">
                                {{ formatCurrency(selectedService.price) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Pagado</span>
                            <span
                                class="font-semibold text-emerald-600 dark:text-emerald-300"
                            >
                                {{ formatCurrency(selectedService.paid_amount) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Saldo</span>
                            <span class="font-semibold text-foreground">
                                {{ formatCurrency(selectedService.balance_due) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            Facturacion
                        </p>
                        <p class="mt-1 text-sm text-foreground">
                            {{ selectedService.billing_type_label }}
                            <span v-if="selectedService.billing_cycle_label">
                                / {{ selectedService.billing_cycle_label }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <p
                            class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            Proxima renovacion
                        </p>
                        <p class="mt-1 text-sm font-semibold text-primary">
                            {{ formatDate(selectedService.next_renewal_date) }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-6 text-center"
            >
                <p class="text-sm text-muted-foreground">
                    Selecciona un servicio para ver su contexto operativo.
                </p>
            </div>
        </CardContent>
    </Card>
</template>
