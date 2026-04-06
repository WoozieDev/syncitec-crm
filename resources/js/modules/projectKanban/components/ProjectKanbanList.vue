<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
import type { ProjectKanbanTask } from '@/modules/projectKanban/types';
import {
    formatTaskLabel,
    taskPriorityClasses,
    taskStatusClasses,
    textOrFallback,
} from '@/modules/projectKanban/helpers';

defineProps<{
    items: ProjectKanbanTask[];
}>();

const emit = defineEmits<{
    (e: 'view-task', task: ProjectKanbanTask): void;
    (e: 'edit-task', task: ProjectKanbanTask): void;
    (e: 'delete-task', task: ProjectKanbanTask): void;
    (e: 'toggle-task', task: ProjectKanbanTask, completed: boolean): void;
}>();

const handleCheckedChange = (
    task: ProjectKanbanTask,
    value: boolean | 'indeterminate',
) => {
    emit('toggle-task', task, Boolean(value));
};
</script>

<template>
    <section
        class="overflow-hidden rounded-3xl border border-border/60 bg-card shadow-sm"
    >
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-muted/45">
                    <tr>
                        <th
                            class="px-5 py-4 text-[11px] font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            Tarea
                        </th>
                        <th
                            class="px-5 py-4 text-[11px] font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            Completada
                        </th>
                        <th
                            class="px-5 py-4 text-[11px] font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            Estado
                        </th>
                        <th
                            class="px-5 py-4 text-[11px] font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            Prioridad
                        </th>
                        <th
                            class="px-5 py-4 text-[11px] font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            Columna
                        </th>
                        <th
                            class="px-5 py-4 text-[11px] font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            Orden
                        </th>
                        <th
                            class="px-5 py-4 text-right text-[11px] font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="task in items"
                        :key="task.id"
                        class="group border-t border-border/50 transition-colors hover:bg-muted/20"
                    >
                        <td class="px-5 py-4 align-top">
                            <div class="space-y-1.5">
                                <button
                                    type="button"
                                    class="text-left font-semibold text-foreground transition-colors hover:text-primary"
                                    :class="
                                        task.is_completed
                                            ? 'line-through opacity-70'
                                            : ''
                                    "
                                    @click="emit('view-task', task)"
                                >
                                    {{ task.title }}
                                </button>
                                <p
                                    class="line-clamp-2 max-w-md text-sm text-muted-foreground"
                                >
                                    {{
                                        textOrFallback(
                                            task.description,
                                            'Sin descripcion registrada.',
                                        )
                                    }}
                                </p>
                            </div>
                        </td>

                        <td class="px-5 py-4 align-top">
                            <Checkbox
                                :checked="task.is_completed"
                                @update:checked="
                                    handleCheckedChange(task, $event)
                                "
                            />
                        </td>

                        <td class="px-5 py-4 align-top">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold uppercase"
                                :class="
                                    taskStatusClasses[task.status] ??
                                    'bg-muted text-muted-foreground'
                                "
                            >
                                {{ task.status_label }}
                            </span>
                        </td>

                        <td class="px-5 py-4 align-top">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold uppercase"
                                :class="
                                    taskPriorityClasses[
                                        (task.priority ?? '').toLowerCase()
                                    ] ?? 'bg-muted text-muted-foreground'
                                "
                            >
                                {{
                                    formatTaskLabel(
                                        task.priority ?? 'sin prioridad',
                                    )
                                }}
                            </span>
                        </td>

                        <td class="px-5 py-4 align-top">
                            {{ task.module?.name ?? 'Sin columna' }}
                        </td>

                        <td
                            class="px-5 py-4 align-top font-semibold text-foreground"
                        >
                            {{ task.order }}
                        </td>

                        <td class="px-5 py-4 align-top">
                            <div class="flex items-center justify-end gap-1">
                                <button
                                    type="button"
                                    class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                    title="Ver detalle"
                                    @click="emit('view-task', task)"
                                >
                                    <Eye class="size-4" />
                                </button>

                                <button
                                    type="button"
                                    class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                    title="Editar"
                                    @click="emit('edit-task', task)"
                                >
                                    <Pencil class="size-4" />
                                </button>

                                <button
                                    type="button"
                                    class="inline-flex size-9 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                                    title="Eliminar"
                                    @click="emit('delete-task', task)"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="items.length === 0"
            class="border-t border-border/50 px-6 py-10 text-center text-sm text-muted-foreground"
        >
            Este proyecto no tiene tareas registradas para mostrar en lista.
        </div>
    </section>
</template>
