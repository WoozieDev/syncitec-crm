<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import ProjectStatusBadge from '@/modules/projects/components/ProjectStatusBadge.vue';
import type { ProjectShowProps } from '@/modules/projects/types';
import { edit, index } from '@/routes/projects';

defineProps<ProjectShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Proyectos',
                href: index(),
            },
            {
                title: 'Detalle del proyecto',
                href: '#',
            },
        ],
    },
});

const currencyFormatter = new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number): string =>
    `S/ ${currencyFormatter.format(value)}`;

const progressClasses: Record<string, string> = {
    planning: 'bg-slate-400 dark:bg-slate-500',
    in_progress: 'bg-blue-500 dark:bg-blue-400',
    in_review: 'bg-cyan-500 dark:bg-cyan-400',
    done: 'bg-emerald-500 dark:bg-emerald-400',
    canceled: 'bg-rose-500 dark:bg-rose-400',
};
</script>

<template>
    <Head :title="`Proyecto - ${project.name}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col gap-6">
            <div class="flex items-start justify-between gap-3">
                <Heading
                    variant="small"
                    :title="project.name"
                    :description="`Codigo del proyecto: ${project.project_code}`"
                />
                <Link :href="edit(project.id)">
                    <Button variant="outline">Editar proyecto</Button>
                </Link>
            </div>

            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Resumen general</CardTitle>
                    <CardDescription>
                        Vista rapida del estado, el saldo y el cronograma.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Cliente</p>
                        <p class="mt-1 font-medium">{{ project.client.display_name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Estado</p>
                        <div class="mt-1">
                            <ProjectStatusBadge
                                :label="project.status_label"
                                :tone="project.status_tone"
                            />
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Monto total</p>
                        <p class="mt-1 font-medium">{{ formatCurrency(project.total_amount) }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Saldo pendiente</p>
                        <p class="mt-1 font-medium">{{ formatCurrency(project.balance_due) }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Monto pagado</p>
                        <p class="mt-1 font-medium">{{ formatCurrency(project.paid_amount) }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Fecha de inicio</p>
                        <p class="mt-1 font-medium">{{ project.start_date ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Fecha estimada de cierre</p>
                        <p class="mt-1 font-medium">{{ project.due_date ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <p class="text-xs font-semibold text-muted-foreground uppercase">Avance</p>
                            <span class="text-xs font-semibold text-muted-foreground">
                                {{ project.progress_percent }}% · {{ project.progress_label }}
                            </span>
                        </div>
                        <div class="h-2.5 rounded-full bg-muted">
                            <div
                                class="h-2.5 rounded-full transition-[width]"
                                :class="
                                    progressClasses[project.status_tone] ??
                                    progressClasses.planning
                                "
                                :style="{ width: `${project.progress_percent}%` }"
                            />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Descripcion</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ project.description }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Notas internas</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ project.notes || 'Sin notas registradas.' }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
