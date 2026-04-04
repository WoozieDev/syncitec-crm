<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Eye,
    Pencil,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import type {
    PaginationLink,
    ProjectPayment,
} from '@/modules/projectPayments/types';
import { edit, show } from '@/routes/project-payments';

const props = defineProps<{
    items: ProjectPayment[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
}>();

const emit = defineEmits<{
    (e: 'delete', payment: ProjectPayment): void;
}>();

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const previousLink = computed(() => props.links[0] ?? null);
const nextLink = computed(() => props.links[props.links.length - 1] ?? null);

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
        return '-';
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
) => {
    if (!value || value.trim().length === 0) {
        return fallback;
    }

    return value;
};
</script>

<template>
    <section
        class="rounded-4xl border border-border/60 bg-muted/50 p-4 shadow-sm sm:p-6 dark:bg-slate-900/40"
    >
        <div
            class="hidden px-6 pb-3 lg:grid lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1.1fr)_minmax(0,0.7fr)_minmax(0,0.9fr)_minmax(0,0.8fr)_minmax(0,1.25fr)_auto]"
        >
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Proyecto
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Cliente
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Monto
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Fecha
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Metodo
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Notas
            </p>
            <p
                class="text-right text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Acciones
            </p>
        </div>

        <div class="space-y-4">
            <Card
                v-for="payment in items"
                :key="payment.id"
                class="gap-0 rounded-3xl border-border/40 py-0 transition-colors hover:bg-accent/30"
            >
                <div
                    class="grid grid-cols-1 gap-4 p-4 md:p-5 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1.1fr)_minmax(0,0.7fr)_minmax(0,0.9fr)_minmax(0,0.8fr)_minmax(0,1.25fr)_auto] lg:items-center lg:gap-3"
                >
                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Proyecto
                        </p>
                        <p class="text-sm font-bold text-foreground">
                            {{ payment.project?.name ?? 'Proyecto no disponible' }}
                        </p>
                        <p class="text-[11px] text-muted-foreground">
                            #{{ payment.id }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Cliente
                        </p>
                        <p class="text-sm text-foreground">
                            {{
                                payment.project?.client.display_name ??
                                'Sin cliente'
                            }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Monto
                        </p>
                        <p class="text-sm font-semibold text-primary">
                            {{ formatCurrency(payment.amount) }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Fecha
                        </p>
                        <p class="text-sm text-foreground">
                            {{ formatDate(payment.payment_date) }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Metodo
                        </p>
                        <p class="text-sm text-foreground">
                            {{ textOrFallback(payment.payment_method) }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Notas
                        </p>
                        <p class="truncate text-sm text-muted-foreground">
                            {{ textOrFallback(payment.notes, 'Sin notas') }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <Link
                            :href="show(payment.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Ver detalle"
                        >
                            <Eye class="size-4" />
                        </Link>

                        <Link
                            :href="edit(payment.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Editar"
                        >
                            <Pencil class="size-4" />
                        </Link>

                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                            title="Eliminar"
                            @click="emit('delete', payment)"
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
                    No se encontraron pagos con los filtros actuales.
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
                de {{ total.toLocaleString('es-ES') }} pagos
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
