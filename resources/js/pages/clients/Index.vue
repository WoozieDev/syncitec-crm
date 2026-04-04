<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Search, Users } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import ClientsTable from '@/modules/clients/components/ClientsTable.vue';

import { useClientsIndex } from '@/modules/clients/composables/useClientsIndex';
import type { ClientIndexProps } from '@/modules/clients/types';
import { create, index } from '@/routes/clients';

const props = defineProps<ClientIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes',
                href: index(),
            },
        ],
    },
});

const { clients, search, totalClients, pageFrom, pageTo, handleDelete } =
    useClientsIndex(props);
</script>

<template>
    <Head title="Clientes" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"
            >
                <div class="space-y-4">
                    <h1
                        class="text-3xl font-black tracking-tight text-foreground sm:text-4xl"
                    >
                        Base de clientes
                    </h1>

                    <div class="flex flex-wrap items-center gap-3">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/10 px-3 py-1.5 text-sm text-primary"
                        >
                            <Users class="size-4" />
                            <span class="font-semibold">
                                {{ totalClients.toLocaleString('es-ES') }}
                                clientes
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end lg:max-w-2xl"
                >
                    <div class="relative w-full sm:max-w-sm">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar por nombre, empresa, email o pais"
                            class="h-11 w-full rounded-full border border-border/60 bg-background/70 pr-4 pl-10 text-sm transition outline-none focus:border-primary/20 focus-visible:ring-2 focus-visible:ring-primary/20"
                        />
                    </div>

                    <Link :href="create()">
                        <Button
                            class="h-11 w-full cursor-pointer rounded-xl px-5 font-semibold shadow-sm shadow-primary/20 sm:w-auto"
                        >
                            <Plus class="size-4" />
                            <span>Nuevo cliente</span>
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <ClientsTable
            :items="clients.data"
            :links="clients.links"
            :from="pageFrom"
            :to="pageTo"
            :total="totalClients"
            @delete="handleDelete"
        />
    </div>
</template>
