<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Filter, Plus, Search, Wrench } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import ServicesTable from '@/modules/services/components/ServicesTable.vue';
import { useServicesIndex } from '@/modules/services/composables/useServicesIndex';
import type { ServiceIndexProps } from '@/modules/services/types';
import { create, index } from '@/routes/services';

const props = defineProps<ServiceIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Servicios',
                href: index(),
            },
        ],
    },
});

const {
    services,
    overview,
    clients,
    serviceTypes,
    statusOptions,
    search,
    clientId,
    status,
    serviceTypeId,
    totalServices,
    pageFrom,
    pageTo,
    handleDelete,
} = useServicesIndex(props);
</script>

<template>
    <Head title="Servicios" />

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
                            <Wrench class="size-4" />
                            <span class="font-semibold">
                                {{ totalServices.toLocaleString('es-ES') }}
                                servicios
                            </span>
                        </div>

                        <div>
                            <h1
                                class="text-3xl font-black tracking-tight text-foreground sm:text-4xl"
                            >
                                Servicios recurrentes
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                                Administra renovaciones, costos y estado
                                operativo de cada servicio vinculado a tus
                                clientes.
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
                                placeholder="Buscar por servicio, cliente, tipo o proveedor"
                                class="h-11 w-full rounded-full border border-border/60 bg-background/70 pr-4 pl-10 text-sm transition outline-none focus:border-primary/20 focus-visible:ring-2 focus-visible:ring-primary/20"
                            />
                        </div>

                        <Link :href="create()">
                            <Button
                                class="h-11 w-full cursor-pointer rounded-xl px-5 font-semibold shadow-sm shadow-primary/20 sm:w-auto"
                            >
                                <Plus class="size-4" />
                                <span>Nuevo servicio</span>
                            </Button>
                        </Link>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-4">
                    <article
                        class="rounded-2xl border border-border/60 bg-background/75 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Total servicios
                        </p>
                        <p class="mt-2 text-3xl font-black tracking-tight">
                            {{ overview.total_services.toLocaleString('es-ES') }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Activos
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-emerald-700 dark:text-emerald-200"
                        >
                            {{ overview.active_services }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-amber-500/20 bg-amber-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Por vencer
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-amber-700 dark:text-amber-200"
                        >
                            {{ overview.expiring_services }}
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-rose-500/20 bg-rose-500/10 px-5 py-4"
                    >
                        <p class="text-xs font-semibold text-muted-foreground">
                            Vencidos
                        </p>
                        <p
                            class="mt-2 text-3xl font-black tracking-tight text-rose-700 dark:text-rose-200"
                        >
                            {{ overview.expired_services }}
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
                        <select
                            v-model="clientId"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Todos los clientes</option>
                            <option
                                v-for="client in clients"
                                :key="client.id"
                                :value="String(client.id)"
                            >
                                {{ client.label }}
                            </option>
                        </select>

                        <select
                            v-model="status"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Todos los estados</option>
                            <option
                                v-for="option in statusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>

                        <select
                            v-model="serviceTypeId"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Todos los tipos</option>
                            <option
                                v-for="serviceType in serviceTypes"
                                :key="serviceType.id"
                                :value="String(serviceType.id)"
                            >
                                {{ serviceType.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <ServicesTable
            :items="services.data"
            :links="services.links"
            :from="pageFrom"
            :to="pageTo"
            :total="totalServices"
            @delete="handleDelete"
        />
    </div>
</template>
