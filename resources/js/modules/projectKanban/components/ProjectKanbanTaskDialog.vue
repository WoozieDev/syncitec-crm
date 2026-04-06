<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { formatTaskLabel, textOrFallback } from '@/modules/projectKanban/helpers';
import type {
    ProjectKanbanModule,
    ProjectKanbanOption,
    ProjectKanbanProject,
    ProjectKanbanTask,
    ProjectKanbanTaskFormData,
} from '@/modules/projectKanban/types';
import { store, update } from '@/routes/project-tasks';

const props = defineProps<{
    open: boolean;
    mode: 'create' | 'edit' | 'view';
    project: ProjectKanbanProject;
    modules: ProjectKanbanModule[];
    task: ProjectKanbanTask | null;
    defaultModuleId: number | null;
    statusOptions: ProjectKanbanOption[];
    priorityOptions: ProjectKanbanOption[];
    currentView: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'saved'): void;
    (e: 'edit-request', task: ProjectKanbanTask): void;
}>();

const form = useForm<ProjectKanbanTaskFormData>({
    project_id: '',
    module_id: '',
    title: '',
    description: '',
    status: 'pendiente',
    priority: '',
    order: '0',
});

const dialogTitle = computed(() => {
    if (props.mode === 'create') {
        return 'Nueva tarea';
    }

    if (props.mode === 'edit') {
        return 'Editar tarea';
    }

    return 'Detalle de tarea';
});

const dialogDescription = computed(() => {
    if (props.mode === 'create') {
        return 'Registra la tarea dentro de una columna real del proyecto.';
    }

    if (props.mode === 'edit') {
        return 'Ajusta columna, prioridad o estado de finalizacion sin salir del Kanban.';
    }

    return 'Consulta el contenido y el contexto operativo de la tarea seleccionada.';
});

const syncForm = () => {
    if (props.mode === 'create') {
        form.defaults({
            project_id: String(props.project.id),
            module_id:
                props.defaultModuleId !== null
                    ? String(props.defaultModuleId)
                    : (props.modules[0]?.id ?? '').toString(),
            title: '',
            description: '',
            status: props.statusOptions[0]?.value ?? 'pendiente',
            priority: '',
            order: '0',
        });
    } else if (props.task) {
        form.defaults({
            project_id: String(props.task.project_id),
            module_id: props.task.module_id ? String(props.task.module_id) : '',
            title: props.task.title,
            description: props.task.description ?? '',
            status: props.task.status,
            priority: props.task.priority ?? '',
            order: String(props.task.order),
        });
    }

    form.reset();
    form.clearErrors();
};

watch(
    () => [props.open, props.mode, props.task?.id, props.defaultModuleId, props.modules.length],
    ([open]) => {
        if (open) {
            syncForm();
        }
    },
    { immediate: true },
);

const close = () => {
    form.clearErrors();
    emit('close');
};

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved');
            close();
        },
    };

    if (props.mode === 'create') {
        form.submit(
            store({
                query: {
                    return_to: 'kanban',
                    return_view: props.currentView,
                },
            }),
            options,
        );

        return;
    }

    if (!props.task) {
        return;
    }

    form.submit(
        update(props.task.id, {
            query: {
                return_to: 'kanban',
                return_view: props.currentView,
            },
        }),
        options,
    );
};
</script>

<template>
    <Dialog :open="open" @update:open="(value) => !value && close()">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ dialogTitle }}</DialogTitle>
                <DialogDescription>
                    {{ dialogDescription }}
                </DialogDescription>
            </DialogHeader>

            <div v-if="mode === 'view' && task" class="space-y-6">
                <div class="space-y-2">
                    <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                        Titulo
                    </p>
                    <p class="text-lg font-bold text-foreground">
                        {{ task.title }}
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                            Columna
                        </p>
                        <p class="mt-1 text-sm font-semibold text-foreground">
                            {{ task.module?.name ?? 'Sin columna' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                            Estado
                        </p>
                        <p class="mt-1 text-sm font-semibold text-foreground">
                            {{ task.status_label }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                            Prioridad
                        </p>
                        <p class="mt-1 text-sm font-semibold text-foreground">
                            {{
                                formatTaskLabel(
                                    task.priority ?? 'sin prioridad',
                                )
                            }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                            Orden
                        </p>
                        <p class="mt-1 text-sm font-semibold text-foreground">
                            {{ task.order }}
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-border/60 bg-muted/25 p-4">
                    <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                        Descripcion
                    </p>
                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                        {{
                            textOrFallback(
                                task.description,
                                'Sin descripcion registrada para esta tarea.',
                            )
                        }}
                    </p>
                </div>

                <DialogFooter class="gap-2 sm:justify-between">
                    <Button type="button" variant="outline" @click="close">
                        Cerrar
                    </Button>

                    <Button
                        type="button"
                        @click="emit('edit-request', task)"
                    >
                        Editar tarea
                    </Button>
                </DialogFooter>
            </div>

            <form v-else class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="task-title">Titulo</Label>
                        <Input
                            id="task-title"
                            v-model="form.title"
                            type="text"
                            placeholder="Ej. Ajustar la pauta del lanzamiento"
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="task-module">Columna</Label>
                        <select
                            id="task-module"
                            v-model="form.module_id"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Selecciona una columna</option>
                            <option
                                v-for="module in modules"
                                :key="module.id"
                                :value="String(module.id)"
                            >
                                {{ module.order }}. {{ module.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.module_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="task-order">Orden</Label>
                        <Input
                            id="task-order"
                            v-model="form.order"
                            type="number"
                            min="0"
                            step="1"
                        />
                        <InputError :message="form.errors.order" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="task-status">Estado</Label>
                        <select
                            id="task-status"
                            v-model="form.status"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option
                                v-for="option in statusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="task-priority">Prioridad</Label>
                        <select
                            id="task-priority"
                            v-model="form.priority"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
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
                        <InputError :message="form.errors.priority" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="task-description">Descripcion</Label>
                        <Textarea
                            id="task-description"
                            v-model="form.description"
                            rows="5"
                            placeholder="Describe el objetivo operativo de esta tarea."
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <Button type="button" variant="outline" @click="close">
                        Cancelar
                    </Button>

                    <Button type="submit" :disabled="form.processing">
                        {{
                            form.processing
                                ? 'Guardando...'
                                : mode === 'create'
                                  ? 'Guardar tarea'
                                  : 'Actualizar tarea'
                        }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
