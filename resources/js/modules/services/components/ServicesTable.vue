<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Eye,
    Pencil,
    Trash2,
    Wrench,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import ServiceStatusBadge from '@/modules/services/components/ServiceStatusBadge.vue';
import type { PaginationLink, Service } from '@/modules/services/types';
import { edit, show } from '@/routes/services';

const props = defineProps<{
    items: Service[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
}>();

const emit = defineEmits<{
    (e: 'delete', service: Service): void;
}>();

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const previousLink = computed(() => props.links[0] ?? null);
const nextLink = computed(() => props.links[props.links.length - 1] ?? null);

const renewalClasses: Record<string, string> = {
    expired: 'text-rose-700 dark:text-rose-200',
    expiring: 'text-amber-700 dark:text-amber-200',
    scheduled: 'text-foreground',
    none: 'text-muted-foreground',
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

    const parsedDate = new Date(`${value}T00:00:00`);

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
</script>

<template>
    <section
        class="rounded-4xl border border-border/60 bg-muted/45 p-4 shadow-sm sm:p-6 dark:bg-slate-900/40"
    >
        <div
            class="hidden px-6 pb-3 lg:grid lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1.1fr)_minmax(0,0.9fr)_minmax(0,1fr)_minmax(0,0.8fr)_minmax(0,0.9fr)_minmax(0,0.85fr)_auto]"
        >
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Servicio
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Cliente
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Tipo
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Proveedor
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Precio
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Proxima renovacion
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Estado
            </p>
            <p
                class="text-right text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Acciones
            </p>
        </div>

        <div class="space-y-4">
            <Card
                v-for="service in items"
                :key="service.id"
                class="gap-0 rounded-3xl border-border/40 py-0 transition-colors hover:bg-accent/25"
            >
                <div
                    class="grid grid-cols-1 gap-4 p-4 md:p-5 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1.1fr)_minmax(0,0.9fr)_minmax(0,1fr)_minmax(0,0.8fr)_minmax(0,0.9fr)_minmax(0,0.85fr)_auto] lg:items-center lg:gap-3"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="inline-flex size-11 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary"
                        >
                            <Wrench class="size-5" />
                        </div>

                        <div>
                            <Link
                                :href="show(service.id)"
                                class="text-sm font-bold text-foreground transition-colors hover:text-primary"
                            >
                                {{ service.name }}
                            </Link>
                            <p class="text-[11px] text-muted-foreground">
                                {{ service.billing_type_label }}
                                <span v-if="service.billing_cycle_label">
                                    / {{ service.billing_cycle_label }}
                                </span>
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
                            {{ service.client.display_name }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ textOrFallback(service.client.name, 'Sin contacto') }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Tipo
                        </p>
                        <p class="text-sm font-medium text-foreground">
                            {{ textOrFallback(service.service_type.name, 'Sin tipo') }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Proveedor
                        </p>
                        <p class="text-sm font-medium text-foreground">
                            {{ textOrFallback(service.provider?.name, 'Sin proveedor') }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Precio
                        </p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ formatCurrency(service.price) }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Proxima renovacion
                        </p>
                        <p
                            class="text-sm font-semibold"
                            :class="
                                renewalClasses[service.renewal_state] ??
                                renewalClasses.scheduled
                            "
                        >
                            {{ formatDate(service.next_renewal_date) }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ service.renewal_label }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Estado
                        </p>
                        <ServiceStatusBadge
                            :label="service.status_label"
                            :tone="service.status_tone"
                        />
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <Link
                            :href="show(service.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Ver detalle"
                        >
                            <Eye class="size-4" />
                        </Link>

                        <Link
                            :href="edit(service.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Editar"
                        >
                            <Pencil class="size-4" />
                        </Link>

                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                            title="Eliminar"
                            @click="emit('delete', service)"
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
                    No se encontraron servicios con los filtros actuales.
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
                de {{ total.toLocaleString('es-ES') }} servicios
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
