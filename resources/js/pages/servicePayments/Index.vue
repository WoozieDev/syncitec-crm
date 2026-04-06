<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Check, CreditCard, Filter, Plus, Search } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
} from '@/components/ui/combobox';
import ServicePaymentsTable from '@/modules/servicePayments/components/ServicePaymentsTable.vue';
import { useServicePaymentsIndex } from '@/modules/servicePayments/composables/useServicePaymentsIndex';
import type { ServicePaymentIndexProps } from '@/modules/servicePayments/types';
import { create, index } from '@/routes/service-payments';

const props = defineProps<ServicePaymentIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pagos de servicios',
                href: index(),
            },
        ],
    },
});

const {
    payments,
    overview,
    services,
    providers,
    paymentMethods,
    search,
    serviceId,
    paymentMethod,
    providerId,
    totalPayments,
    pageFrom,
    pageTo,
    handleDelete,
} = useServicePaymentsIndex(props);

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;

const serviceOptions = computed(() => [
    { id: '', label: 'Todos' },
    ...services.value.map((service) => ({
        id: String(service.id),
        label: service.label,
    })),
]);

const selectedService = computed({
    get: () =>
        serviceOptions.value.find((service) => service.id === serviceId.value) ??
        serviceOptions.value[0],
    set: (service?: { id: string; label: string }) => {
        serviceId.value = service?.id ?? '';
    },
});
</script>

<template>
    <Head title="Pagos de servicios" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div class="flex flex-col gap-6">
                <div
                    class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between"
                >
                    <div class="space-y-4">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/10 px-3 py-1.5 text-sm text-primary"
                        >
                            <CreditCard class="size-4" />
                            <span class="font-semibold">
                                {{ totalPayments.toLocaleString('es-ES') }} pagos
                            </span>
                        </div>

                        <div>
                            <h1
                                class="text-3xl font-black tracking-tight text-foreground sm:text-4xl"
                            >
                                Pagos de servicios
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                                Administra pagos asociados a servicios recurrentes,
                                con contexto de cliente, proveedor y renovacion.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end xl:max-w-2xl"
                    >
                        <div class="relative w-full sm:max-w-sm">
                            <Search
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar por servicio, cliente, proveedor o notas"
                                class="h-11 w-full rounded-full border border-border/60 bg-background/70 pr-4 pl-10 text-sm transition outline-none focus:border-primary/20 focus-visible:ring-2 focus-visible:ring-primary/20"
                            />
                        </div>

                        <Link :href="create()">
                            <Button
                                class="h-11 w-full cursor-pointer rounded-xl px-5 font-semibold shadow-sm shadow-primary/20 sm:w-auto"
                            >
                                <Plus class="size-4" />
                                <span>Nuevo pago</span>
                            </Button>
                        </Link>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-4">
                    <article
                        class="rounded-2xl border border-border/60 bg-background/75 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Total registrado
                        </p>
                        <p class="mt-2 text-3xl font-black tracking-tight">
                            {{ overview.total_payments.toLocaleString('es-ES') }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-cyan-500/20 bg-cyan-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Total pagado
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-cyan-700 dark:text-cyan-200"
                        >
                            {{ formatCurrency(overview.total_paid) }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-blue-500/20 bg-blue-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Servicios con pagos
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-blue-700 dark:text-blue-200"
                        >
                            {{ overview.unique_services }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-amber-500/20 bg-amber-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Promedio por pago
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-amber-700 dark:text-amber-200"
                        >
                            {{ formatCurrency(overview.average_payment) }}
                        </p>
                    </article>
                </div>

                <div
                    class="flex flex-col gap-3 rounded-2xl border border-border/60 bg-background/75 p-4 lg:flex-row lg:items-center"
                >
                    <div class="flex items-center gap-2 text-muted-foreground">
                        <Filter class="size-4" />
                        <span class="text-xs font-semibold tracking-[0.15em] uppercase">
                            Filtros
                        </span>
                    </div>

                    <div class="grid flex-1 gap-3 sm:grid-cols-3">
                        <Combobox v-model="selectedService" by="id">
                            <ComboboxAnchor class="w-full">
                                <ComboboxInput
                                    class="h-10"
                                    placeholder="Todos"
                                    :display-value="(value) => value?.label ?? ''"
                                />
                            </ComboboxAnchor>
                            <ComboboxList class="w-[var(--reka-combobox-trigger-width)]">
                                <ComboboxEmpty>No se encontraron servicios.</ComboboxEmpty>
                                <ComboboxGroup>
                                    <ComboboxItem
                                        v-for="service in serviceOptions"
                                        :key="service.id || 'empty'"
                                        :value="service"
                                    >
                                        {{ service.label }}
                                        <ComboboxItemIndicator>
                                            <Check class="size-4" />
                                        </ComboboxItemIndicator>
                                    </ComboboxItem>
                                </ComboboxGroup>
                            </ComboboxList>
                        </Combobox>

                        <select
                            v-model="paymentMethod"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Todos los metodos</option>
                            <option
                                v-for="method in paymentMethods"
                                :key="method"
                                :value="method"
                            >
                                {{ method }}
                            </option>
                        </select>

                        <select
                            v-model="providerId"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Todos los proveedores</option>
                            <option
                                v-for="provider in providers"
                                :key="provider.id"
                                :value="String(provider.id)"
                            >
                                {{ provider.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <ServicePaymentsTable
            :items="payments.data"
            :links="payments.links"
            :from="pageFrom"
            :to="pageTo"
            :total="totalPayments"
            @delete="handleDelete"
        />
    </div>
</template>
