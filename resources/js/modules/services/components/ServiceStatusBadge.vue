<script setup lang="ts">
const props = defineProps<{
    label: string;
    tone: string;
}>();

const toneClasses: Record<string, string> = {
    active: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200',
    pending: 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200',
    suspended:
        'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200',
    canceled:
        'bg-rose-500/15 text-rose-700 dark:bg-rose-400/15 dark:text-rose-200',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.12em] uppercase"
        :class="toneClasses[props.tone] ?? toneClasses.pending"
    >
        {{ props.label }}
    </span>
</template>
