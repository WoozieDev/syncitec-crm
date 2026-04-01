<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Pencil,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { Client, PaginationLink } from '@/modules/clients/types';
import { edit } from '@/routes/clients';

const props = defineProps<{
    items: Client[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
}>();

const emit = defineEmits<{
    (e: 'delete', client: Client): void;
}>();

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const previousLink = computed(() => props.links[0] ?? null);
const nextLink = computed(() => props.links[props.links.length - 1] ?? null);

const formatCreatedAt = (value?: string | null) => {
    if (!value) {
        return {
            date: '-',
            time: '-',
        };
    }

    const parsedDate = new Date(value.replace(' ', 'T'));

    if (Number.isNaN(parsedDate.getTime())) {
        return {
            date: '-',
            time: '-',
        };
    }

    return {
        date: parsedDate.toLocaleDateString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        }),
        time: parsedDate.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
        }),
    };
};
</script>

<template>
    <section
        class="rounded-[2rem] bg-muted/70 p-6 shadow-sm dark:bg-slate-900/60 sm:p-8"
    >
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-4 text-left">
                <thead>
                    <tr
                        class="text-[11px] font-bold uppercase tracking-[0.18em] text-muted-foreground"
                    >
                        <th class="pb-4 pl-6">Name</th>
                        <th class="pb-4">Company</th>
                        <th class="pb-4">Email</th>
                        <th class="pb-4">Phone</th>
                        <th class="pb-4">Country</th>
                        <th class="pb-4">Created Date</th>
                        <th class="pb-4 pr-6 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="client in items" :key="client.id">
                        <td
                            class="rounded-l-3xl bg-card py-5 pl-6 shadow-sm dark:bg-[#0b1120]"
                        >
                            <div>
                                <p class="text-sm font-bold text-foreground">
                                    {{ client.name }}
                                </p>
                                <p class="text-[11px] text-muted-foreground">
                                    ID: #{{ client.id }}
                                </p>
                            </div>
                        </td>

                        <td class="bg-card py-5 shadow-sm dark:bg-[#0b1120]">
                            <p class="text-sm font-medium text-foreground">
                                {{ client.company }}
                            </p>
                        </td>

                        <td class="bg-card py-5 shadow-sm dark:bg-[#0b1120]">
                            <p class="text-sm font-medium text-foreground">
                                {{ client.email }}
                            </p>
                        </td>

                        <td class="bg-card py-5 shadow-sm dark:bg-[#0b1120]">
                            <p class="text-sm text-foreground">
                                {{ client.phone }}
                            </p>
                        </td>

                        <td class="bg-card py-5 shadow-sm dark:bg-[#0b1120]">
                            <p class="text-sm text-foreground">
                                {{ client.country }}
                            </p>
                        </td>

                        <td class="bg-card py-5 shadow-sm dark:bg-[#0b1120]">
                            <p class="text-sm text-foreground">
                                {{ formatCreatedAt(client.created_at).date }}
                            </p>
                            <p class="text-[11px] text-muted-foreground">
                                {{ formatCreatedAt(client.created_at).time }}
                            </p>
                        </td>

                        <td
                            class="rounded-r-3xl bg-card py-5 pr-6 text-right shadow-sm dark:bg-[#0b1120]"
                        >
                            <div class="flex justify-end gap-2">
                                <Link
                                    :href="edit(client.id)"
                                    class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                    title="Edit"
                                >
                                    <Pencil class="size-4" />
                                </Link>

                                <button
                                    type="button"
                                    class="inline-flex size-9 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                                    title="Delete"
                                    @click="emit('delete', client)"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td
                            colspan="7"
                            class="rounded-3xl bg-card px-6 py-12 text-center text-sm text-muted-foreground shadow-sm dark:bg-[#0b1120]"
                        >
                            No customers found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="mt-8 flex flex-col gap-4 text-xs font-medium text-muted-foreground md:flex-row md:items-center md:justify-between"
        >
            <p>
                Showing <span class="font-bold text-foreground">{{ from }} - {{ to }}</span>
                of {{ total.toLocaleString() }} customers
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
