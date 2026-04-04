<script setup lang="ts">
import { computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { ProjectPaymentProjectOption } from '@/modules/projectPayments/types';

const props = defineProps<{
    projects: ProjectPaymentProjectOption[];
    selectedProjectId: string;
}>();

const selectedProject = computed(() =>
    props.projects.find((project) => String(project.id) === props.selectedProjectId),
);

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;

const formatLabel = (value?: string | null): string => {
    if (!value) {
        return 'Sin dato';
    }

    return value
        .replaceAll('_', ' ')
        .split(' ')
        .map((part) =>
            part.length > 0
                ? part.charAt(0).toUpperCase() + part.slice(1)
                : part,
        )
        .join(' ');
};
</script>

<template>
    <Card class="h-fit border-border/60">
        <CardHeader>
            <CardTitle class="text-base">Contexto del proyecto</CardTitle>
        </CardHeader>

        <CardContent>
            <div v-if="selectedProject" class="space-y-5">
                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Proyecto
                    </p>
                    <p class="mt-1 text-sm font-semibold text-foreground">
                        {{ selectedProject.name }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Cliente
                    </p>
                    <p class="mt-1 text-sm text-foreground">
                        {{ selectedProject.client_display_name }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Estado
                    </p>
                    <p class="mt-1 text-sm text-foreground">
                        {{ formatLabel(selectedProject.status) }}
                    </p>
                </div>

                <div class="space-y-2 rounded-xl border border-border/60 bg-muted/25 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-foreground">Total</span>
                        <span class="font-semibold text-foreground">
                            {{ formatCurrency(selectedProject.total_amount) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-foreground">Pagado</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-300">
                            {{ formatCurrency(selectedProject.paid_amount) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-foreground">Saldo</span>
                        <span class="font-semibold text-foreground">
                            {{ formatCurrency(selectedProject.balance_due) }}
                        </span>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-6 text-center"
            >
                <p class="text-sm text-muted-foreground">
                    Selecciona un proyecto para ver su contexto financiero.
                </p>
            </div>
        </CardContent>
    </Card>
</template>
