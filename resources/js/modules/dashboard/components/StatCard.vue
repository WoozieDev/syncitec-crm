<script setup lang="ts">
import type { StatCardItem } from '@/modules/dashboard/types/dashboard';

defineProps<{
    item: StatCardItem;
}>();

const toneClasses: Record<NonNullable<StatCardItem['tone']>, string> = {
    primary: 'bg-primary/12 text-primary dark:bg-primary/18 dark:text-blue-200',
    sky: 'bg-sky-500/12 text-sky-600 dark:bg-sky-400/16 dark:text-sky-300',
    danger:
        'bg-destructive/12 text-destructive dark:bg-destructive/16 dark:text-red-300',
    slate:
        'bg-slate-500/10 text-slate-700 dark:bg-slate-400/12 dark:text-slate-200',
    indigo:
        'bg-indigo-500/12 text-indigo-600 dark:bg-indigo-400/16 dark:text-indigo-200',
};

const badgeClasses: Record<string, string> = {
    default: 'bg-primary/10 text-primary dark:bg-primary/16 dark:text-blue-200',
    success:
        'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/16 dark:text-emerald-300',
    warning:
        'bg-amber-500/10 text-amber-700 dark:bg-amber-400/16 dark:text-amber-300',
    danger:
        'bg-destructive/10 text-destructive dark:bg-destructive/16 dark:text-red-300',
};
</script>

<template>
    <article
        class="rounded-2xl border border-border/50 bg-card p-5 shadow-sm dark:border-white/6 dark:bg-[#0b1120]"
    >
        <div class="mb-5 flex items-start justify-between gap-3">
            <div
                class="flex size-11 items-center justify-center rounded-xl"
                :class="toneClasses[item.tone ?? 'primary']"
            >
                <component :is="item.icon" class="size-5" />
            </div>

            <div class="flex items-center gap-2">
                <span
                    v-if="item.badge"
                    class="rounded-full px-2 py-1 text-[10px] font-bold uppercase"
                    :class="badgeClasses[item.badgeVariant ?? 'default']"
                >
                    {{ item.badge }}
                </span>
                <span
                    v-if="item.hasAlertDot"
                    class="size-2 rounded-full bg-destructive"
                />
            </div>
        </div>

        <div class="space-y-1">
            <p class="text-4xl font-bold tracking-tight text-foreground">
                {{ item.value }}
            </p>
            <p
                class="max-w-[10rem] text-[11px] font-semibold uppercase tracking-[0.14em] text-muted-foreground"
            >
                {{ item.description }}
            </p>
        </div>
    </article>
</template>
