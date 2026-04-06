<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import {
    Eye,
    Pencil,
    Plus,
    SquareKanban,
    Trash2,
} from 'lucide-vue-next';
import {
    formatTaskLabel,
    kanbanColumnBadgeClass,
    kanbanColumnSurfaceClass,
    kanbanColumnTitleClass,
    taskPriorityClasses,
    taskStatusClasses,
    textOrFallback,
} from '@/modules/projectKanban/helpers';
import type {
    ProjectKanbanColumn,
    ProjectKanbanModule,
    ProjectKanbanTask,
} from '@/modules/projectKanban/types';

defineProps<{
    columns: ProjectKanbanColumn[];
    modules: ProjectKanbanModule[];
}>();

const emit = defineEmits<{
    (e: 'create-column'): void;
    (e: 'edit-column', column: ProjectKanbanColumn): void;
    (e: 'delete-column', column: ProjectKanbanColumn): void;
    (e: 'create-task', moduleId: number | null): void;
    (e: 'view-task', task: ProjectKanbanTask): void;
    (e: 'edit-task', task: ProjectKanbanTask): void;
    (e: 'delete-task', task: ProjectKanbanTask): void;
    (e: 'toggle-task', task: ProjectKanbanTask, completed: boolean): void;
    (e: 'move-task', task: ProjectKanbanTask, moduleId: number): void;
}>();

const handleMoveTask = (task: ProjectKanbanTask, event: Event) => {
    const target = event.target as HTMLSelectElement;
    const moduleId = Number(target.value);

    if (!Number.isFinite(moduleId) || moduleId <= 0 || moduleId === task.module_id) {
        return;
    }

    emit('move-task', task, moduleId);
};

const handleCheckedChange = (
    task: ProjectKanbanTask,
    value: boolean | 'indeterminate',
) => {
    emit('toggle-task', task, Boolean(value));
};
</script>

<template>
    <section class="overflow-x-auto pb-2">
        <div class="flex min-w-max gap-5">
            <div
                v-for="(column, index) in columns"
                :key="column.key"
                class="w-[340px] shrink-0"
            >
                <div class="mb-3 flex items-start justify-between gap-3 px-1">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <h2
                                class="text-xs font-black tracking-[0.18em] uppercase"
                                :class="kanbanColumnTitleClass(index)"
                            >
                                {{ column.label }}
                            </h2>

                            <span
                                class="rounded-full px-2.5 py-1 text-[10px] font-bold"
                                :class="kanbanColumnBadgeClass(index)"
                            >
                                {{ column.count }}
                            </span>
                        </div>

                        <p
                            v-if="column.is_unassigned"
                            class="text-xs text-muted-foreground"
                        >
                            Tareas legacy sin columna asignada.
                        </p>
                    </div>

                    <div
                        v-if="!column.is_unassigned"
                        class="flex items-center gap-1"
                    >
                        <button
                            type="button"
                            class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Nueva tarea"
                            @click="emit('create-task', column.id)"
                        >
                            <Plus class="size-4" />
                        </button>

                        <button
                            type="button"
                            class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Editar columna"
                            @click="emit('edit-column', column)"
                        >
                            <Pencil class="size-4" />
                        </button>

                        <button
                            type="button"
                            class="inline-flex size-8 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10 disabled:cursor-not-allowed disabled:opacity-40"
                            title="Eliminar columna"
                            :disabled="!column.can_delete"
                            @click="emit('delete-column', column)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>

                <div
                    class="space-y-4 rounded-3xl border p-3"
                    :class="kanbanColumnSurfaceClass(index)"
                >
                    <article
                        v-for="task in column.tasks"
                        :key="task.id"
                        class="group rounded-2xl border border-border/60 bg-card/95 p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <label
                                class="flex items-center gap-2 text-xs font-semibold text-muted-foreground"
                            >
                                <Checkbox
                                    :checked="task.is_completed"
                                    @update:checked="
                                        handleCheckedChange(task, $event)
                                    "
                                />
                                <span>
                                    {{
                                        task.is_completed
                                            ? 'Completada'
                                            : 'Pendiente'
                                    }}
                                </span>
                            </label>

                            <span
                                class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase"
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
                        </div>

                        <div class="mt-4 space-y-2">
                            <button
                                type="button"
                                class="block text-left text-sm font-bold text-foreground transition-colors hover:text-primary"
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
                                class="line-clamp-3 text-sm leading-6 text-muted-foreground"
                            >
                                {{
                                    textOrFallback(
                                        task.description,
                                        'Sin descripcion registrada.',
                                    )
                                }}
                            </p>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold uppercase"
                                :class="
                                    taskStatusClasses[task.status] ??
                                    'bg-muted text-muted-foreground'
                                "
                            >
                                {{ task.status_label }}
                            </span>

                            <span
                                class="inline-flex items-center rounded-full bg-muted px-2.5 py-1 text-[10px] font-bold text-muted-foreground uppercase"
                            >
                                Orden {{ task.order }}
                            </span>
                        </div>

                        <div
                            class="mt-4 space-y-3 border-t border-border/50 pt-4"
                        >
                            <div class="grid gap-2">
                                <label
                                    class="text-[11px] font-semibold tracking-[0.14em] text-muted-foreground uppercase"
                                >
                                    Mover a
                                </label>
                                <select
                                    class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                                    :value="
                                        task.module_id
                                            ? String(task.module_id)
                                            : ''
                                    "
                                    @change="handleMoveTask(task, $event)"
                                >
                                    <option value="" disabled>
                                        Selecciona una columna
                                    </option>
                                    <option
                                        v-for="module in modules"
                                        :key="module.id"
                                        :value="String(module.id)"
                                    >
                                        {{ module.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <div
                                    class="flex items-center gap-2 text-xs text-muted-foreground"
                                >
                                    <SquareKanban class="size-4" />
                                    <span>{{
                                        task.module?.name ?? 'Sin columna'
                                    }}</span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Ver detalle"
                                        @click="emit('view-task', task)"
                                    >
                                        <Eye class="size-4" />
                                    </button>

                                    <button
                                        type="button"
                                        class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Editar"
                                        @click="emit('edit-task', task)"
                                    >
                                        <Pencil class="size-4" />
                                    </button>

                                    <button
                                        type="button"
                                        class="inline-flex size-8 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                                        title="Eliminar"
                                        @click="emit('delete-task', task)"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>

                    <button
                        v-if="!column.is_unassigned"
                        type="button"
                        class="flex w-full items-center justify-center gap-2 rounded-2xl border border-dashed border-border/70 bg-background/60 p-4 text-sm font-semibold text-muted-foreground transition-colors hover:border-primary/30 hover:text-foreground"
                        @click="emit('create-task', column.id)"
                    >
                        <Plus class="size-4" />
                        <span>Nueva tarea</span>
                    </button>

                    <div
                        v-if="column.tasks.length === 0"
                        class="rounded-2xl border border-dashed border-border/70 bg-background/60 p-6 text-center text-sm text-muted-foreground"
                    >
                        No hay tareas en esta columna.
                    </div>
                </div>
            </div>

            <button
                type="button"
                class="flex h-[140px] w-[280px] shrink-0 items-center justify-center gap-3 self-start rounded-3xl border border-dashed border-border/70 bg-background/70 px-6 text-left text-sm font-semibold text-muted-foreground transition-colors hover:border-primary/30 hover:text-foreground"
                @click="emit('create-column')"
            >
                <Plus class="size-5" />
                <span>Nueva columna</span>
            </button>
        </div>
    </section>
</template>
