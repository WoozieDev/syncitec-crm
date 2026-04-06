<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    CalendarDays,
    CheckCircle2,
    Clock3,
    Eye,
    GripVertical,
    Pencil,
    Trash2,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Card } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    dueDateTone,
    formatDate,
    formatLabel,
    plannerColumnClasses,
    priorityBadgeClasses,
    priorityDotClasses,
    statusBadgeClasses,
    textPreview,
} from '@/modules/personalTasks/helpers';
import type {
    PersonalTask,
    PersonalTaskBoardColumn,
} from '@/modules/personalTasks/types';
import { edit, show } from '@/routes/personal-tasks';

const props = defineProps<{
    board: PersonalTaskBoardColumn[];
    backlogTasks: PersonalTask[];
    completedTasks: PersonalTask[];
}>();

const emit = defineEmits<{
    (e: 'delete', task: PersonalTask): void;
    (e: 'toggle-completion', payload: { task: PersonalTask; value: boolean }): void;
    (e: 'move', payload: { task: PersonalTask; bucket: string }): void;
}>();

const draggingTaskId = ref<number | null>(null);

const taskById = computed(() => {
    const map = new Map<number, PersonalTask>();

    props.board.forEach((column) => {
        column.tasks.forEach((task) => map.set(task.id, task));
    });

    props.backlogTasks.forEach((task) => map.set(task.id, task));

    return map;
});

const startDrag = (task: PersonalTask) => {
    draggingTaskId.value = task.id;
};

const endDrag = () => {
    draggingTaskId.value = null;
};

const dropTask = (bucket: string) => {
    if (draggingTaskId.value === null) {
        return;
    }

    const task = taskById.value.get(draggingTaskId.value);

    if (!task) {
        return;
    }

    emit('move', { task, bucket });
    draggingTaskId.value = null;
};
</script>

<template>
    <section class="space-y-6">
        <div class="grid gap-5 xl:grid-cols-3">
            <div
                v-for="column in board"
                :key="column.key"
                class="min-h-72 rounded-3xl border p-4"
                :class="
                    plannerColumnClasses[column.key] ??
                    'border-border/60 bg-background/80'
                "
                @dragover.prevent
                @drop="dropTask(column.key)"
            >
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <p
                            class="text-xs font-black tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            {{ column.label }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ column.count }} activas
                        </p>
                    </div>

                    <span
                        class="rounded-full border border-border/60 bg-background/80 px-3 py-1 text-xs font-semibold text-muted-foreground"
                    >
                        {{ column.count }}
                    </span>
                </div>

                <div class="space-y-4">
                    <article
                        v-for="task in column.tasks"
                        :key="task.id"
                        draggable="true"
                        class="group rounded-3xl border border-border/60 bg-card/95 p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
                        @dragstart="startDrag(task)"
                        @dragend="endDrag"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <Checkbox
                                    :model-value="task.is_completed"
                                    class="mt-1"
                                    @update:model-value="
                                        emit('toggle-completion', {
                                            task,
                                            value: Boolean($event),
                                        })
                                    "
                                />

                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full border px-2.5 py-1 text-[11px] font-semibold"
                                            :class="
                                                priorityBadgeClasses[
                                                    task.priority ?? ''
                                                ] ??
                                                'border-border/60 bg-muted text-muted-foreground'
                                            "
                                        >
                                            <span
                                                class="size-2 rounded-full"
                                                :class="
                                                    priorityDotClasses[
                                                        task.priority ?? ''
                                                    ] ?? 'bg-muted-foreground/40'
                                                "
                                            />
                                            {{
                                                formatLabel(
                                                    task.priority ??
                                                        'sin prioridad',
                                                )
                                            }}
                                        </span>

                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                            :class="
                                                statusBadgeClasses[
                                                    task.status
                                                ] ??
                                                'bg-muted text-muted-foreground'
                                            "
                                        >
                                            {{ formatLabel(task.status) }}
                                        </span>
                                    </div>

                                    <Link
                                        :href="show(task.id)"
                                        class="block text-base font-bold text-foreground transition-colors hover:text-primary"
                                    >
                                        {{ task.title }}
                                    </Link>
                                </div>
                            </div>

                            <GripVertical
                                class="size-4 text-muted-foreground/60 transition-colors group-hover:text-foreground"
                            />
                        </div>

                        <p class="mt-3 text-sm leading-6 text-muted-foreground">
                            {{ textPreview(task.description) }}
                        </p>

                        <div
                            class="mt-4 flex items-center justify-between gap-3 border-t border-border/50 pt-4"
                        >
                            <div class="flex items-center gap-2 text-xs">
                                <CalendarDays class="size-4 text-muted-foreground" />
                                <span :class="dueDateTone(task)">
                                    {{ formatDate(task.due_date, 'Sin fecha') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1">
                                <Link
                                    :href="show(task.id)"
                                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                    title="Ver detalle"
                                >
                                    <Eye class="size-4" />
                                </Link>

                                <Link
                                    :href="edit(task.id)"
                                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                    title="Editar"
                                >
                                    <Pencil class="size-4" />
                                </Link>

                                <button
                                    type="button"
                                    class="inline-flex size-8 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                                    title="Eliminar"
                                    @click="emit('delete', task)"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </div>
                    </article>

                    <Card
                        v-if="column.tasks.length === 0"
                        class="rounded-3xl border-dashed bg-background/80 py-10 text-center text-sm text-muted-foreground"
                    >
                        Arrastra tareas aqui o crea una nueva para este bloque.
                    </Card>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <section
                class="rounded-3xl border border-border/60 bg-card/70 p-5"
                @dragover.prevent
                @drop="dropTask('backlog')"
            >
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black tracking-tight">
                            Backlog / Sin fecha
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Tareas que aun no entran al ciclo inmediato.
                        </p>
                    </div>
                    <span
                        class="rounded-full border border-border/60 bg-background/80 px-3 py-1 text-xs font-semibold text-muted-foreground"
                    >
                        {{ backlogTasks.length }}
                    </span>
                </div>

                <div class="space-y-3">
                    <article
                        v-for="task in backlogTasks"
                        :key="task.id"
                        draggable="true"
                        class="rounded-2xl border border-border/60 bg-background/80 p-4"
                        @dragstart="startDrag(task)"
                        @dragend="endDrag"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <Link
                                    :href="show(task.id)"
                                    class="text-sm font-bold text-foreground transition-colors hover:text-primary"
                                >
                                    {{ task.title }}
                                </Link>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ textPreview(task.description) }}
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex size-8 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                                title="Eliminar"
                                @click="emit('delete', task)"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </article>

                    <Card
                        v-if="backlogTasks.length === 0"
                        class="rounded-2xl border-dashed bg-background/80 py-8 text-center text-sm text-muted-foreground"
                    >
                        Sin tareas en backlog.
                    </Card>
                </div>
            </section>

            <section class="rounded-3xl border border-border/60 bg-card/70 p-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black tracking-tight">
                            Completadas recientes
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Historial corto para reabrir o verificar avance.
                        </p>
                    </div>
                    <CheckCircle2 class="size-5 text-emerald-500" />
                </div>

                <div class="space-y-3">
                    <article
                        v-for="task in completedTasks"
                        :key="task.id"
                        class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4"
                    >
                        <div class="flex items-start gap-3">
                            <Checkbox
                                :model-value="task.is_completed"
                                class="mt-1"
                                @update:model-value="
                                    emit('toggle-completion', {
                                        task,
                                        value: Boolean($event),
                                    })
                                "
                            />

                            <div class="min-w-0 flex-1">
                                <Link
                                    :href="show(task.id)"
                                    class="text-sm font-bold text-foreground transition-colors hover:text-primary"
                                >
                                    {{ task.title }}
                                </Link>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ textPreview(task.description) }}
                                </p>
                                <div
                                    class="mt-3 flex items-center gap-2 text-xs text-muted-foreground"
                                >
                                    <Clock3 class="size-4" />
                                    <span>
                                        Completada {{ formatDate(task.completed_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <Card
                        v-if="completedTasks.length === 0"
                        class="rounded-2xl border-dashed bg-background/80 py-8 text-center text-sm text-muted-foreground"
                    >
                        Aun no hay tareas completadas.
                    </Card>
                </div>
            </section>
        </div>
    </section>
</template>
