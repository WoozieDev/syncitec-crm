<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { MessageSquareMore } from 'lucide-vue-next';
import OverduePaymentsTable from '@/modules/dashboard/components/OverduePaymentsTable.vue';
import QuickActions from '@/modules/dashboard/components/QuickActions.vue';
import RecentActivitiesCard from '@/modules/dashboard/components/RecentActivitiesCard.vue';
import RenewalsCard from '@/modules/dashboard/components/RenewalsCard.vue';
import StatCard from '@/modules/dashboard/components/StatCard.vue';
import UrgentTasksCard from '@/modules/dashboard/components/UrgentTasksCard.vue';
import {
    overduePayments,
    recentActivities,
    renewals,
    stats,
    urgentTasks,
} from '@/modules/dashboard/data/dashboard';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 sm:p-6">
        <div
            class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between"
        >
            <div>
                <p
                    class="text-xs font-bold tracking-[0.2em] text-muted-foreground uppercase"
                >
                    Overview Dashboard
                </p>
                <h1 class="text-3xl font-bold tracking-tight">
                    Workspace Pulse
                </h1>
            </div>

            <div>
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-muted px-3 py-1 text-xs font-medium text-muted-foreground shadow-sm dark:bg-slate-800/80"
                >
                    <span class="h-2 w-2 rounded-full bg-emerald-500" />
                    Operational Status: Optimal
                </span>
            </div>
        </div>

        <section
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6"
        >
            <StatCard v-for="item in stats" :key="item.title" :item="item" />
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <OverduePaymentsTable :items="overduePayments" />
                <QuickActions />
            </div>

            <div class="space-y-6 xl:col-span-4">
                <UrgentTasksCard :items="urgentTasks" />
                <RecentActivitiesCard :items="recentActivities" />
                <RenewalsCard :items="renewals" />
            </div>
        </section>

        <button
            type="button"
            class="fixed bottom-6 right-6 flex size-14 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-2xl shadow-primary/25 transition-transform hover:scale-105"
        >
            <MessageSquareMore class="size-6" />
        </button>
    </div>
</template>
