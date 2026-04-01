<script setup lang="ts">
import type { ActivityItem } from '@/modules/dashboard/types/dashboard';

defineProps<{
    items: ActivityItem[];
}>();

const dotClass = (variant?: string) => {
    if (variant === 'primary') {
        return 'bg-primary ring-primary/20';
    }

    if (variant === 'success') {
        return 'bg-emerald-500 ring-emerald-500/20';
    }

    return 'bg-slate-400 ring-slate-400/20 dark:bg-slate-500 dark:ring-slate-500/20';
};
</script>

<template>
    <section
        class="rounded-2xl border border-border/50 bg-card p-6 shadow-sm dark:border-white/6 dark:bg-[#131c2d] sm:p-7"
    >
        <h3 class="mb-6 text-2xl font-bold tracking-tight text-foreground">
            Recent Activities
        </h3>

        <div
            class="relative space-y-7 before:absolute before:left-[7px] before:top-2 before:bottom-2 before:w-px before:bg-border"
        >
            <article
                v-for="item in items"
                :key="item.id"
                class="relative pl-8"
            >
                <div
                    class="absolute left-0 top-1 size-4 rounded-full border-4 border-card ring-2 dark:border-[#131c2d]"
                    :class="dotClass(item.dotVariant)"
                />
                <p
                    class="text-[11px] font-bold uppercase tracking-[0.14em] text-muted-foreground"
                >
                    {{ item.dateLabel }}
                </p>
                <h4 class="mt-1 text-sm font-semibold text-foreground">
                    {{ item.title }}
                </h4>
                <p class="text-xs leading-5 text-muted-foreground">
                    {{ item.description }}
                </p>
            </article>
        </div>
    </section>
</template>
