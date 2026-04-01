<script setup lang="ts">
import { ArrowRight } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import type { OverduePaymentItem } from '@/modules/dashboard/types/dashboard';

defineProps<{
    items: OverduePaymentItem[];
}>();

const avatarToneClasses: Record<
    NonNullable<OverduePaymentItem['avatarTone']>,
    string
> = {
    primary:
        'bg-primary/12 text-primary dark:bg-primary/18 dark:text-blue-200',
    sky: 'bg-sky-500/12 text-sky-600 dark:bg-sky-400/16 dark:text-sky-300',
    slate:
        'bg-slate-500/10 text-slate-700 dark:bg-slate-400/12 dark:text-slate-200',
};
</script>

<template>
    <section
        class="rounded-2xl border border-border/50 bg-card p-6 shadow-sm dark:border-white/6 dark:bg-[#0b1120] sm:p-7"
    >
        <div class="mb-7 flex items-center justify-between gap-4">
            <h3 class="text-2xl font-bold tracking-tight text-foreground">
                Overdue Payments Summary
            </h3>
            <button
                type="button"
                class="text-sm font-semibold text-primary transition-colors hover:text-primary/80"
            >
                View All History
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr
                        class="text-[11px] font-bold uppercase tracking-[0.16em] text-muted-foreground"
                    >
                        <th class="pb-5 pr-5">Customer</th>
                        <th class="pb-5 pr-5">Service</th>
                        <th class="pb-5 pr-5">Amount</th>
                        <th class="pb-5 pr-5">Due Date</th>
                        <th class="pb-5 pr-5">Status</th>
                        <th class="pb-5 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="item in items"
                        :key="item.id"
                        class="border-t border-border/40 first:border-t-0"
                    >
                        <td class="py-4 pr-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-10 items-center justify-center rounded-full text-sm font-bold"
                                    :class="
                                        cn(
                                            avatarToneClasses[
                                                item.avatarTone ?? 'primary'
                                            ],
                                        )
                                    "
                                >
                                    {{ item.initials }}
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-foreground">
                                        {{ item.customer }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ item.companyId }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 pr-5 text-sm font-medium text-foreground">
                            {{ item.service }}
                        </td>
                        <td class="py-4 pr-5 text-sm font-bold text-foreground">
                            {{ item.amount }}
                        </td>
                        <td class="py-4 pr-5 text-sm font-semibold text-destructive">
                            {{ item.dueDate }}
                        </td>
                        <td class="py-4 pr-5">
                            <span
                                class="rounded-full bg-destructive/10 px-2.5 py-1 text-[10px] font-bold uppercase text-destructive"
                            >
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="py-4 text-right">
                            <button
                                type="button"
                                class="inline-flex size-8 items-center justify-center rounded-lg text-primary transition-colors hover:bg-primary/10"
                            >
                                <ArrowRight class="size-4" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
