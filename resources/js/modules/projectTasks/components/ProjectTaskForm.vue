<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import {
    ArrowDownUp,
    FileText,
    FolderKanban,
    Layers3,
    ListTodo,
    SquareKanban,
} from 'lucide-vue-next';
import { computed, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type {
    ProjectTaskFormData,
    ProjectTaskPriorityOption,
    ProjectTaskProjectOption,
    ProjectTaskStatusOption,
} from '@/modules/projectTasks/types';

const props = defineProps<{
    form: InertiaForm<ProjectTaskFormData>;
    projects: ProjectTaskProjectOption[];
    statusOptions: ProjectTaskStatusOption[];
    priorityOptions: ProjectTaskPriorityOption[];
    submitLabel: string;
    processingLabel: string;
    cancelHref: string;
    description: string;
}>();

defineEmits<{
    (e: 'submit'): void;
}>();

const availableModules = computed(() => {
    const selectedProject = props.projects.find(
        (project) => String(project.id) === props.form.project_id,
    );

    return selectedProject?.modules ?? [];
});

watch(
    () => props.form.project_id,
    () => {
        const moduleExists = availableModules.value.some(
            (module) => String(module.id) === props.form.module_id,
        );

        if (!moduleExists) {
            props.form.module_id = '';
        }
    },
    { immediate: true },
);
</script>

<template>
    <Card class="border-border/60">
        <CardHeader>
            <CardTitle>Registro de tarea</CardTitle>
            <CardDescription>
                {{ description }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form class="space-y-8" @submit.prevent="$emit('submit')">
                <section class="space-y-6">
                    <div class="grid gap-2">
                        <Label
                            for="project_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Proyecto
                        </Label>
                        <div class="relative">
                            <FolderKanban
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <select
                                id="project_id"
                                v-model="form.project_id"
                                class="h-10 w-full rounded-md border border-input bg-background py-2 pr-3 pl-10 text-sm"
                            >
                                <option value="">Selecciona un proyecto</option>
                                <option
                                    v-for="project in projects"
                                    :key="project.id"
                                    :value="String(project.id)"
                                >
                                    {{ project.label }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="form.errors.project_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="module_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Modulo
                        </Label>
                        <div class="relative">
                            <SquareKanban
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <select
                                id="module_id"
                                v-model="form.module_id"
                                class="h-10 w-full rounded-md border border-input bg-background py-2 pr-3 pl-10 text-sm"
                            >
                                <option value="">Sin modulo</option>
                                <option
                                    v-for="module in availableModules"
                                    :key="module.id"
                                    :value="String(module.id)"
                                >
                                    {{ module.order }}. {{ module.name }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="form.errors.module_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="title"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Titulo
                        </Label>
                        <div class="relative">
                            <ListTodo
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="Ej. Ajustar integracion del modulo de reportes"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="description"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Descripcion
                        </Label>
                        <div class="relative">
                            <FileText
                                class="pointer-events-none absolute top-4 left-3 size-4 text-muted-foreground"
                            />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="5"
                                placeholder="Describe el objetivo operativo de la tarea."
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.description" />
                    </div>
                </section>

                <section
                    class="grid gap-6 rounded-2xl border border-border/60 bg-muted/25 p-5 md:grid-cols-2 xl:grid-cols-4"
                >
                    <div class="grid gap-2">
                        <Label
                            for="status"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Estado
                        </Label>
                        <div class="relative">
                            <Layers3
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <select
                                id="status"
                                v-model="form.status"
                                class="h-10 w-full rounded-md border border-input bg-background py-2 pr-3 pl-10 text-sm"
                            >
                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="priority"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Prioridad
                        </Label>
                        <div class="relative">
                            <Layers3
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="h-10 w-full rounded-md border border-input bg-background py-2 pr-3 pl-10 text-sm"
                            >
                                <option value="">Sin prioridad</option>
                                <option
                                    v-for="option in priorityOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="form.errors.priority" />
                    </div>

                    <div class="grid gap-2 md:col-span-2 xl:col-span-1">
                        <Label
                            for="order"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Orden
                        </Label>
                        <div class="relative">
                            <ArrowDownUp
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="order"
                                v-model="form.order"
                                type="number"
                                min="0"
                                step="1"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.order" />
                    </div>
                </section>

                <div
                    class="flex flex-wrap items-center justify-end gap-3 border-t pt-6"
                >
                    <Link :href="cancelHref">
                        <Button type="button" variant="outline" class="cursor-pointer">
                            Cancelar
                        </Button>
                    </Link>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="cursor-pointer"
                    >
                        {{ form.processing ? processingLabel : submitLabel }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
