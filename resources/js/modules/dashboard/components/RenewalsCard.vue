<script setup lang="ts">
import { CalendarClock } from 'lucide-vue-next';
import type { RenewalItem } from '@/modules/dashboard/types/dashboard';

defineProps<{
    items: RenewalItem[];
}>();
</script>

<template>
    <section
        class="rounded-2xl border border-border/50 bg-card p-6 shadow-sm dark:border-white/6 dark:bg-[#0b1120]"
    >
        <div class="mb-4 flex items-center gap-3">
            <CalendarClock class="size-5 text-sky-500" />
            <h3 class="text-xl font-bold tracking-tight text-foreground">
                Renewing this Month
            </h3>
        </div>

        <div class="space-y-3">
            <div
                v-for="item in items"
                :key="item.id"
                class="flex items-center justify-between gap-4 text-sm"
            >
                <span class="font-medium text-foreground">{{ item.name }}</span>
                <span class="text-muted-foreground">{{ item.date }}</span>
            </div>
        </div>

        <div class="mt-5">
            <div class="h-1.5 rounded-full bg-muted">
                <div class="h-1.5 w-[65%] rounded-full bg-sky-500" />
            </div>
            <p
                class="mt-2 text-right text-[10px] font-bold uppercase tracking-[0.18em] text-muted-foreground"
            >
                65% retention rate
            </p>
        </div>
    </section>
</template>
