<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CalendarDays,
    CheckCheck,
    ClipboardList,
    LayoutGrid,
    List,
    Plus,
    SquareKanban,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import ProjectKanbanBoard from '@/modules/projectKanban/components/ProjectKanbanBoard.vue';
import ProjectKanbanColumnDialog from '@/modules/projectKanban/components/ProjectKanbanColumnDialog.vue';
import ProjectKanbanList from '@/modules/projectKanban/components/ProjectKanbanList.vue';
import ProjectKanbanTaskDialog from '@/modules/projectKanban/components/ProjectKanbanTaskDialog.vue';
import { textOrFallback } from '@/modules/projectKanban/helpers';
import type {
    ProjectKanbanColumn,
    ProjectKanbanProps,
    ProjectKanbanTask,
} from '@/modules/projectKanban/types';
import ProjectStatusBadge from '@/modules/projects/components/ProjectStatusBadge.vue';
import { destroy, update } from '@/routes/project-tasks';
import { index, kanban as projectKanban, show } from '@/routes/projects';

const props = defineProps<ProjectKanbanProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Proyectos',
                href: index(),
            },
            {
                title: 'Kanban',
                href: '#',
            },
        ],
    },
});

const currentView = computed(() => props.view);
const hasModules = computed(() => props.project.modules.length > 0);
const shouldShowEmptyState = computed(
    () => !hasModules.value && props.project.overview.unassigned_tasks === 0,
);

const isTaskDialogOpen = ref(false);
const taskDialogMode = ref<'create' | 'edit' | 'view'>('create');
const selectedTask = ref<ProjectKanbanTask | null>(null);
const defaultTaskModuleId = ref<number | null>(props.project.modules[0]?.id ?? null);

const isColumnDialogOpen = ref(false);
const columnDialogMode = ref<'create' | 'edit'>('create');
const selectedColumn = ref<ProjectKanbanColumn | null>(null);

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

const buildTaskPayload = (
    task: ProjectKanbanTask,
    overrides: Partial<{
        module_id: number | '';
        status: string;
    }> = {},
) => ({
    project_id: task.project_id,
    module_id: overrides.module_id ?? task.module_id ?? '',
    title: task.title,
    description: task.description ?? '',
    status: overrides.status ?? task.status,
    priority: task.priority ?? '',
    order: task.order,
    return_to: 'kanban',
    return_view: currentView.value,
});

const openCreateColumn = () => {
    columnDialogMode.value = 'create';
    selectedColumn.value = null;
    isColumnDialogOpen.value = true;
};

const openEditColumn = (column: ProjectKanbanColumn) => {
    if (column.is_unassigned) {
        return;
    }

    columnDialogMode.value = 'edit';
    selectedColumn.value = column;
    isColumnDialogOpen.value = true;
};

const openCreateTask = (moduleId: number | null = null) => {
    if (!hasModules.value) {
        openCreateColumn();
        return;
    }

    taskDialogMode.value = 'create';
    selectedTask.value = null;
    defaultTaskModuleId.value = moduleId ?? props.project.modules[0]?.id ?? null;
    isTaskDialogOpen.value = true;
};

const openEditTask = (task: ProjectKanbanTask) => {
    taskDialogMode.value = 'edit';
    selectedTask.value = task;
    defaultTaskModuleId.value = task.module_id;
    isTaskDialogOpen.value = true;
};

const openViewTask = (task: ProjectKanbanTask) => {
    taskDialogMode.value = 'view';
    selectedTask.value = task;
    defaultTaskModuleId.value = task.module_id;
    isTaskDialogOpen.value = true;
};

const closeTaskDialog = () => {
    isTaskDialogOpen.value = false;
};

const closeColumnDialog = () => {
    isColumnDialogOpen.value = false;
};

const handleDeleteTask = (task: ProjectKanbanTask) => {
    if (!confirm(`Seguro que deseas eliminar la tarea "${task.title}"?`)) {
        return;
    }

    router.delete(destroy(task.id), {
        data: {
            return_to: 'kanban',
            return_view: currentView.value,
        },
        preserveScroll: true,
    });
};

const handleToggleTask = (task: ProjectKanbanTask, completed: boolean) => {
    router.put(
        update(task.id),
        buildTaskPayload(task, {
            status: completed ? 'completada' : 'pendiente',
        }),
        {
            preserveScroll: true,
        },
    );
};

const handleMoveTask = (task: ProjectKanbanTask, moduleId: number) => {
    router.put(
        update(task.id),
        buildTaskPayload(task, {
            module_id: moduleId,
        }),
        {
            preserveScroll: true,
        },
    );
};

const handleDeleteColumn = (column: ProjectKanbanColumn) => {
    if (!column.id || !column.can_delete) {
        return;
    }

    if (!confirm(`Seguro que deseas eliminar la columna "${column.name}"?`)) {
        return;
    }

    router.delete(`/projects/${props.project.id}/modules/${column.id}`, {
        data: {
            view: currentView.value,
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Kanban - ${project.name}`" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div class="flex flex-col gap-6">
                <div
                    class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between"
                >
                    <div class="space-y-4">
                        <div
                            class="flex flex-wrap items-center gap-2 text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            <span>Proyecto</span>
                            <span>/</span>
                            <span>{{ project.client.display_name }}</span>
                            <span>/</span>
                            <span class="text-primary">Kanban</span>
                        </div>

                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <ProjectStatusBadge
                                    :label="project.status_label"
                                    :tone="project.status_tone"
                                />

                                <span
                                    class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-medium text-muted-foreground"
                                >
                                    {{ project.client.display_name }}
                                </span>
                            </div>

                            <div>
                                <h1
                                    class="text-3xl font-black tracking-tight text-foreground sm:text-4xl"
                                >
                                    {{ project.name }}
                                </h1>
                                <p
                                    class="mt-2 max-w-3xl text-sm text-muted-foreground"
                                >
                                    {{
                                        textOrFallback(
                                            project.description,
                                            'Sin descripcion registrada para este proyecto.',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap sm:justify-end"
                    >
                        <Link :href="show(project.id)">
                            <Button
                                variant="outline"
                                class="w-full cursor-pointer sm:w-auto"
                            >
                                <ArrowLeft class="size-4" />
                                <span>Volver al proyecto</span>
                            </Button>
                        </Link>

                        <Button
                            variant="outline"
                            class="w-full cursor-pointer sm:w-auto"
                            @click="openCreateColumn"
                        >
                            <SquareKanban class="size-4" />
                            <span>Nueva columna</span>
                        </Button>

                        <Button
                            class="w-full cursor-pointer sm:w-auto"
                            :disabled="!hasModules"
                            @click="openCreateTask()"
                        >
                            <Plus class="size-4" />
                            <span>Nueva tarea</span>
                        </Button>
                    </div>
                </div>

                <div class="grid gap-4 xl:grid-cols-[1fr_auto] xl:items-center">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <article
                            class="rounded-2xl border border-border/60 bg-background/80 px-5 py-4"
                        >
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Total tareas
                            </p>
                            <p class="mt-2 text-3xl font-black tracking-tight">
                                {{ project.overview.tasks_total }}
                            </p>
                        </article>

                        <article
                            class="rounded-2xl border border-border/60 bg-background/80 px-5 py-4"
                        >
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Columnas
                            </p>
                            <p class="mt-2 text-3xl font-black tracking-tight">
                                {{ project.overview.modules_total }}
                            </p>
                        </article>

                        <article
                            class="rounded-2xl border border-blue-500/20 bg-blue-500/10 px-5 py-4"
                        >
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Activas
                            </p>
                            <p
                                class="mt-2 text-3xl font-black tracking-tight text-blue-700 dark:text-blue-200"
                            >
                                {{ project.overview.active_tasks }}
                            </p>
                        </article>

                        <article
                            class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-4"
                        >
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Completadas
                            </p>
                            <p
                                class="mt-2 text-3xl font-black tracking-tight text-emerald-700 dark:text-emerald-200"
                            >
                                {{ project.overview.completed_tasks }}
                            </p>
                        </article>
                    </div>

                    <div
                        class="inline-flex items-center rounded-full border border-border/60 bg-background/80 p-1"
                    >
                        <Link
                            v-for="option in views"
                            :key="option.key"
                            :href="
                                projectKanban(project.id, {
                                    query: { view: option.key },
                                })
                            "
                            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition-colors"
                            :class="
                                currentView === option.key
                                    ? 'bg-primary text-primary-foreground shadow-sm'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                        >
                            <LayoutGrid
                                v-if="option.key === 'board'"
                                class="size-4"
                            />
                            <List v-else class="size-4" />
                            <span>{{ option.label }}</span>
                        </Link>
                    </div>
                </div>

                <div
                    class="flex flex-wrap items-center gap-4 rounded-2xl border border-border/60 bg-background/75 px-4 py-3 text-sm text-muted-foreground"
                >
                    <div class="flex items-center gap-2">
                        <SquareKanban class="size-4" />
                        <span
                            >{{ project.modules.length }} columnas
                            registradas</span
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <ClipboardList class="size-4" />
                        <span
                            >{{ project.overview.tasks_total }} tareas
                            totales</span
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <CheckCheck class="size-4" />
                        <span
                            >{{
                                project.overview.completed_tasks
                            }}
                            finalizadas</span
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <CalendarDays class="size-4" />
                        <span>
                            {{ formatDate(project.start_date) }} -
                            {{ formatDate(project.due_date) }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="!hasModules"
                    class="rounded-2xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-900 dark:text-amber-100"
                >
                    Primero debes crear una columna del proyecto para empezar a registrar tareas.
                </div>

                <div
                    v-if="project.overview.unassigned_tasks > 0"
                    class="rounded-2xl border border-slate-500/20 bg-slate-500/10 px-4 py-3 text-sm text-muted-foreground"
                >
                    Hay {{ project.overview.unassigned_tasks }} tareas legacy sin columna. Muévelas a una columna real desde el Kanban.
                </div>
            </div>
        </section>

        <section
            v-if="shouldShowEmptyState"
            class="rounded-3xl border border-dashed border-border/70 bg-card/65 p-10 text-center shadow-sm"
        >
            <div class="mx-auto max-w-xl space-y-4">
                <div
                    class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary"
                >
                    <SquareKanban class="size-6" />
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl font-black tracking-tight text-foreground">
                        Empieza creando tu primera columna
                    </h2>
                    <p class="text-sm leading-6 text-muted-foreground">
                        En este flujo las columnas salen de los modulos del proyecto.
                        Primero defines la columna y luego registras las tareas dentro de ella.
                    </p>
                </div>

                <div class="flex justify-center">
                    <Button class="cursor-pointer" @click="openCreateColumn">
                        <Plus class="size-4" />
                        <span>Crear columna</span>
                    </Button>
                </div>
            </div>
        </section>

        <ProjectKanbanBoard
            v-else-if="currentView === 'board'"
            :columns="board"
            :modules="project.modules"
            @create-column="openCreateColumn"
            @edit-column="openEditColumn"
            @delete-column="handleDeleteColumn"
            @create-task="openCreateTask"
            @view-task="openViewTask"
            @edit-task="openEditTask"
            @delete-task="handleDeleteTask"
            @toggle-task="handleToggleTask"
            @move-task="handleMoveTask"
        />

        <ProjectKanbanList
            v-else
            :items="project.tasks"
            @view-task="openViewTask"
            @edit-task="openEditTask"
            @delete-task="handleDeleteTask"
            @toggle-task="handleToggleTask"
        />
    </div>

    <ProjectKanbanTaskDialog
        :open="isTaskDialogOpen"
        :mode="taskDialogMode"
        :project="project"
        :modules="project.modules"
        :task="selectedTask"
        :default-module-id="defaultTaskModuleId"
        :status-options="status_options"
        :priority-options="priority_options"
        :current-view="currentView"
        @close="closeTaskDialog"
        @saved="closeTaskDialog"
        @edit-request="openEditTask"
    />

    <ProjectKanbanColumnDialog
        :open="isColumnDialogOpen"
        :mode="columnDialogMode"
        :project="project"
        :column="selectedColumn"
        :current-view="currentView"
        @close="closeColumnDialog"
        @saved="closeColumnDialog"
    />
</template>
