<script setup lang="ts">
import type { UrgentTaskItem } from '@/modules/dashboard/types/dashboard';

defineProps<{
    items: UrgentTaskItem[];
}>();

const toneClasses: Record<NonNullable<UrgentTaskItem['tone']>, string> = {
    danger: 'border-destructive text-destructive',
    primary: 'border-primary text-primary',
    slate: 'border-slate-400 text-slate-400 dark:border-slate-500 dark:text-slate-400',
};

const badgeClasses: Record<NonNullable<UrgentTaskItem['tone']>, string> = {
    danger:
        'bg-destructive/10 text-destructive dark:bg-destructive/16 dark:text-red-300',
    primary:
        'bg-primary/12 text-primary dark:bg-primary/16 dark:text-blue-200',
    slate:
        'bg-muted text-muted-foreground dark:bg-slate-700/60 dark:text-slate-300',
};
</script>

<template>
    <section
        class="rounded-2xl bg-slate-100 p-6 shadow-sm dark:bg-slate-800/70 sm:p-7"
    >
        <div class="mb-6 flex items-center justify-between gap-4">
            <h3 class="text-2xl font-bold tracking-tight text-foreground">
                Urgent Tasks
            </h3>
            <span
                class="flex size-6 items-center justify-center rounded-full bg-destructive text-[10px] font-black text-destructive-foreground"
            >
                {{ items.length }}
            </span>
        </div>

        <div class="space-y-4">
            <article
                v-for="item in items"
                :key="item.id"
                class="rounded-2xl border border-border/40 bg-card p-4 shadow-sm dark:border-white/6 dark:bg-[#0b1120]"
            >
                <div class="flex items-start gap-4">
                    <div
                        class="mt-1 size-5 rounded-[6px] border-2"
                        :class="toneClasses[item.tone ?? 'slate']"
                    />

                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-semibold text-foreground">
                            {{ item.title }}
                        </h4>
                        <p class="mt-1 text-xs leading-5 text-muted-foreground">
                            {{ item.description }}
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-md px-2 py-1 text-[10px] font-bold uppercase"
                                :class="badgeClasses[item.tone ?? 'slate']"
                            >
                                {{ item.priority }}
                            </span>
                            <span
                                v-if="item.dueLabel"
                                class="text-[10px] font-medium text-muted-foreground"
                            >
                                {{ item.dueLabel }}
                            </span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <button
            type="button"
            class="mt-6 h-11 w-full rounded-xl border border-primary/20 text-xs font-bold uppercase tracking-[0.18em] text-primary transition-colors hover:bg-primary/8"
        >
            View Task Board
        </button>
    </section>
</template>
