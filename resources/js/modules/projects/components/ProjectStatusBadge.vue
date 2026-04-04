<script setup lang="ts">
const props = defineProps<{
    label: string;
    tone: string;
}>();

const badgeClasses: Record<string, string> = {
    planning:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    in_progress:
        'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200',
    in_review:
        'bg-cyan-500/15 text-cyan-700 dark:bg-cyan-400/15 dark:text-cyan-200',
    done: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
    canceled:
        'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.18em] uppercase"
        :class="badgeClasses[props.tone] ?? badgeClasses.planning"
    >
        {{ props.label }}
    </span>
</template>
