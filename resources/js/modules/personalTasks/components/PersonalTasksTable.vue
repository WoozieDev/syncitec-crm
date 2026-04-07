<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import {
    taskGroupClasses,
    formatDate,
    formatLabel,
    priorityBadgeClasses,
    priorityDotClasses,
    statusBadgeClasses,
    textPreview,
} from '@/modules/personalTasks/helpers';
import type {
    PersonalTask,
    PersonalTaskGroup,
} from '@/modules/personalTasks/types';
import { edit, show } from '@/routes/personal-tasks';

defineProps<{
    groupedTasks: PersonalTaskGroup[];
}>();

const emit = defineEmits<{
    (e: 'delete', task: PersonalTask): void;
    (
        e: 'toggle-completion',
        payload: { task: PersonalTask; value: boolean },
    ): void;
}>();

const shouldShowDate = (groupKey: string) =>
    groupKey === 'this_week' || groupKey === 'next_week';
</script>

<template>
    <section class="space-y-5">
        <article
            v-for="group in groupedTasks"
            :key="group.key"
            class="rounded-3xl border p-4 sm:p-5"
            :class="
                taskGroupClasses[group.key] ??
                'border-border/60 bg-background/80'
            "
        >
            <header class="mb-4 flex items-center justify-between gap-3">
                <h2
                    class="text-lg font-black tracking-tight"
                    :class="
                        group.key === 'overdue' || group.key === 'today'
                            ? 'text-foreground'
                            : 'text-foreground/90'
                    "
                >
                    {{ group.label }} ({{ group.count }})
                </h2>
            </header>

            <div
                class="overflow-hidden rounded-2xl border border-border/60 bg-background"
            >
                <div
                    class="grid grid-cols-[minmax(0,1.4fr)_120px_120px_auto] gap-3 border-b border-border/60 px-4 py-3 text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
                    :class="
                        shouldShowDate(group.key)
                            ? 'md:grid-cols-[minmax(0,1.4fr)_120px_120px_130px_auto]'
                            : 'md:grid-cols-[minmax(0,1.6fr)_120px_120px_auto]'
                    "
                >
                    <span>Titulo</span>
                    <span>Prioridad</span>
                    <span>Estado</span>
                    <span v-if="shouldShowDate(group.key)">Fecha</span>
                    <span class="text-right">Acciones</span>
                </div>

                <div
                    v-if="group.tasks.length === 0"
                    class="px-4 py-8 text-center text-sm text-muted-foreground"
                >
                    No hay tareas en este bloque por ahora.
                </div>

                <div v-else>
                    <div
                        v-for="task in group.tasks"
                        :key="task.id"
                        class="grid grid-cols-[minmax(0,1.4fr)_120px_120px_auto] gap-3 border-b border-border/50 px-4 py-3 text-sm last:border-b-0"
                        :class="[
                            shouldShowDate(group.key)
                                ? 'md:grid-cols-[minmax(0,1.4fr)_120px_120px_130px_auto]'
                                : 'md:grid-cols-[minmax(0,1.6fr)_120px_120px_auto]',
                            task.is_completed ? 'opacity-70' : '',
                        ]"
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
                            <div class="min-w-0">
                                <Link
                                    :href="show(task.id)"
                                    class="font-semibold text-foreground hover:text-primary"
                                >
                                    {{ task.title }}
                                </Link>
                                <p
                                    class="line-clamp-1 text-xs text-muted-foreground"
                                >
                                    {{ textPreview(task.description) }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11px] font-semibold"
                                :class="
                                    priorityBadgeClasses[task.priority ?? ''] ??
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
                                        task.priority ?? 'sin prioridad',
                                    )
                                }}
                            </span>
                        </div>

                        <div>
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                :class="
                                    statusBadgeClasses[task.status] ??
                                    'bg-muted text-muted-foreground'
                                "
                            >
                                {{ formatLabel(task.status) }}
                            </span>
                        </div>

                        <div
                            v-if="shouldShowDate(group.key)"
                            class="text-xs text-muted-foreground"
                        >
                            {{ formatDate(task.due_date, '-') }}
                        </div>

                        <div class="flex items-center justify-end gap-1">
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
                </div>
            </div>
        </article>
    </section>
</template>
