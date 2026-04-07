<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Search, Tag } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import ServiceTypesTable from '@/modules/serviceTypes/components/ServiceTypesTable.vue';
import { useServiceTypesIndex } from '@/modules/serviceTypes/composables/useServiceTypesIndex';
import type { ServiceTypeIndexProps } from '@/modules/serviceTypes/types';
import { create, index } from '@/routes/serviceTypes';

const props = defineProps<ServiceTypeIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tipos de servicio',
                href: index(),
            },
        ],
    },
});

const { serviceTypes, search, total, from, to, handleDelete } =
    useServiceTypesIndex(props);
</script>

<template>
    <Head title="Tipos de servicio" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-4">
                    <h1 class="text-3xl font-black tracking-tight text-foreground sm:text-4xl">
                        Tipos de servicio
                    </h1>

                    <div class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/10 px-3 py-1.5 text-sm text-primary">
                        <Tag class="size-4" />
                        <span class="font-semibold">
                            {{ total.toLocaleString('es-ES') }} tipos
                        </span>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end lg:max-w-2xl">
                    <div class="relative w-full sm:max-w-sm">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar tipo de servicio"
                            class="h-11 w-full rounded-full border border-border/60 bg-background/70 pr-4 pl-10 text-sm transition outline-none focus:border-primary/20 focus-visible:ring-2 focus-visible:ring-primary/20"
                        />
                    </div>

                    <Link :href="create()">
                        <Button
                            class="h-11 w-full cursor-pointer rounded-xl px-5 font-semibold shadow-sm shadow-primary/20 sm:w-auto"
                        >
                            <Plus class="size-4" />
                            <span>Nuevo tipo</span>
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <ServiceTypesTable
            :items="serviceTypes.data"
            :links="serviceTypes.links"
            :from="from"
            :to="to"
            :total="total"
            @delete="handleDelete"
        />
    </div>
</template>
