<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import ClientsTable from '@/modules/clients/components/ClientsTable.vue';
import type { Client, ClientIndexProps } from '@/modules/clients/types';
import { create, destroy, index } from '@/routes/clients';

const props = defineProps<ClientIndexProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Customers',
                href: index(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');

watchDebounced(
    search,
    (value) => {
        router.get(
            index(),
            { search: value },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    },
    { debounce: 300, maxWait: 900 },
);

const totalCustomers = computed(() => props.clients.total ?? props.clients.data.length);
const pageFrom = computed(() => props.clients.from ?? 1);
const pageTo = computed(() => props.clients.to ?? props.clients.data.length);

const handleDelete = (client: Client) => {
    if (!confirm(`Are you sure you want to delete ${client.name}?`)) {
        return;
    }

    router.delete(destroy(client.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Customers" />

    <div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-4 sm:p-6">
        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <h1 class="text-4xl font-black tracking-tight text-foreground">
                    Customer Base
                </h1>

                <div class="flex flex-wrap items-center gap-3">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-muted px-3 py-1.5 text-sm text-primary"
                    >
                        <span class="size-2 rounded-full bg-primary" />
                        <span class="font-semibold">
                            {{ totalCustomers.toLocaleString() }} Total
                        </span>
                    </div>

                    <div class="w-full max-w-sm">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search by name, company, email..."
                            class="h-11 w-full rounded-full border border-transparent bg-muted/60 px-4 text-sm outline-none transition focus:border-primary/20 focus:bg-background focus-visible:ring-2 focus-visible:ring-primary/10"
                        />
                    </div>
                </div>
            </div>

            <Link :href="create()">
                <Button class="h-12 rounded-xl px-6 font-semibold shadow-sm shadow-primary/20">
                    <Plus class="size-4" />
                    <span>New Customer</span>
                </Button>
            </Link>
        </section>

        <ClientsTable
            :items="clients.data"
            :links="clients.links"
            :from="pageFrom"
            :to="pageTo"
            :total="totalCustomers"
            @delete="handleDelete"
        />
    </div>
</template>
