<script setup lang="ts">
import { computed } from 'vue';
import {
    Building2,
    CalendarDays,
    FolderKanban,
    SquareKanban,
} from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { ProjectTaskProjectOption } from '@/modules/projectTasks/types';

const props = defineProps<{
    projects: ProjectTaskProjectOption[];
    selectedProjectId: string;
}>();

const selectedProject = computed(
    () =>
        props.projects.find(
            (project) => String(project.id) === props.selectedProjectId,
        ) ?? null,
);

const parseDate = (value?: string | null) => {
    if (!value) {
        return null;
    }

    const parsedDate = new Date(`${value}T00:00:00`);

    return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
};

const formatDate = (value?: string | null): string => {
    const parsedDate = parseDate(value);

    if (!parsedDate) {
        return '-';
    }

    return parsedDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const formatLabel = (value: string | null | undefined): string => {
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
    <Card class="border-border/60">
        <CardHeader>
            <CardTitle>Contexto del proyecto</CardTitle>
            <CardDescription>
                Referencia visual del proyecto y sus modulos disponibles.
            </CardDescription>
        </CardHeader>

        <CardContent v-if="selectedProject" class="space-y-5">
            <div class="flex items-start gap-3">
                <div
                    class="inline-flex size-11 items-center justify-center rounded-xl bg-primary/10 text-primary"
                >
                    <FolderKanban class="size-5" />
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-foreground">
                        {{ selectedProject.name }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        {{ selectedProject.client.display_name }}
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Estado
                    </p>
                    <p class="mt-1 text-sm font-semibold text-foreground">
                        {{ formatLabel(selectedProject.status) }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Modulos
                    </p>
                    <p class="mt-1 text-sm font-semibold text-foreground">
                        {{ selectedProject.modules.length }}
                    </p>
                </div>
            </div>

            <div class="space-y-3 rounded-2xl border border-border/60 bg-muted/25 p-4">
                <div class="flex items-center gap-2 text-sm text-foreground">
                    <Building2 class="size-4 text-muted-foreground" />
                    <span>{{ selectedProject.client.display_name }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-foreground">
                    <CalendarDays class="size-4 text-muted-foreground" />
                    <span>
                        {{ formatDate(selectedProject.start_date) }} -
                        {{ formatDate(selectedProject.due_date) }}
                    </span>
                </div>
            </div>

            <div v-if="selectedProject.modules.length > 0" class="space-y-3">
                <p
                    class="text-[11px] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                >
                    Modulos disponibles
                </p>

                <ol class="space-y-2">
                    <li
                        v-for="module in selectedProject.modules.slice(0, 4)"
                        :key="module.id"
                        class="flex items-center gap-3 rounded-xl border border-border/60 bg-background/80 px-3 py-2"
                    >
                        <span
                            class="inline-flex size-7 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary"
                        >
                            {{ module.order }}
                        </span>
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <SquareKanban class="size-4 text-muted-foreground" />
                            <span>{{ module.name }}</span>
                        </div>
                    </li>
                </ol>
            </div>
        </CardContent>

        <CardContent v-else>
            <div
                class="rounded-2xl border border-dashed border-border/70 bg-muted/20 p-6 text-center text-sm text-muted-foreground"
            >
                Selecciona un proyecto para ver su cliente, estado y modulos.
            </div>
        </CardContent>
    </Card>
</template>
