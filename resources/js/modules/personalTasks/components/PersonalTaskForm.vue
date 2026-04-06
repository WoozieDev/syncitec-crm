<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import {
    CalendarDays,
    CircleCheckBig,
    FileText,
    Flag,
    Layers3,
    ListTodo,
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
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SimpleRichTextEditor from '@/modules/personalTasks/components/SimpleRichTextEditor.vue';
import {
    priorityBadgeClasses,
    priorityDotClasses,
} from '@/modules/personalTasks/helpers';
import type {
    PersonalTaskFormData,
    PersonalTaskPriorityOption,
} from '@/modules/personalTasks/types';

const props = defineProps<{
    form: InertiaForm<PersonalTaskFormData>;
    priorityOptions: PersonalTaskPriorityOption[];
    submitLabel: string;
    processingLabel: string;
    cancelHref: string;
    description: string;
}>();

const form = props.form;

defineEmits<{
    (e: 'submit'): void;
}>();

const statusOptions = [
    { value: 'pendiente', label: 'Pendiente' },
    { value: 'en_progreso', label: 'En progreso' },
    { value: 'completada', label: 'Completada' },
];

watch(
    () => form.is_completed,
    (isCompleted) => {
        if (isCompleted) {
            form.status = 'completada';

            return;
        }

        if (form.status === 'completada') {
            form.status = 'pendiente';
        }
    },
);

const currentPriorityClass = computed(
    () =>
        priorityBadgeClasses[form.priority] ??
        'border-border/60 bg-muted text-muted-foreground',
);

const currentPriorityDot = computed(
    () => priorityDotClasses[form.priority] ?? 'bg-muted-foreground/40',
);
</script>

<template>
    <Card class="border-border/60">
        <CardHeader>
            <CardTitle>Registro de tarea personal</CardTitle>
            <CardDescription>
                {{ description }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form class="space-y-8" @submit.prevent="$emit('submit')">
                <section class="space-y-6">
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
                                placeholder="Ej. Revisar pendientes del dia"
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
                            Descripcion enriquecida
                        </Label>
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <FileText class="size-4" />
                                <span>
                                    Usa formato basico para checklist, enfasis y notas.
                                </span>
                            </div>
                            <SimpleRichTextEditor
                                v-model="form.description"
                                placeholder="Describe el objetivo, pasos o notas clave de la tarea."
                            />
                        </div>
                        <InputError :message="form.errors.description" />
                    </div>
                </section>

                <section
                    class="grid gap-6 rounded-2xl border border-border/60 bg-muted/25 p-5 md:grid-cols-2 xl:grid-cols-3"
                >
                    <div class="grid gap-2">
                        <Label
                            for="due_date"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Fecha objetivo
                        </Label>
                        <div class="relative">
                            <CalendarDays
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.due_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="priority"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Prioridad
                        </Label>
                        <div class="relative">
                            <Flag
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

                        <div
                            class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold"
                            :class="currentPriorityClass"
                        >
                            <span
                                class="size-2.5 rounded-full"
                                :class="currentPriorityDot"
                            />
                            <span>
                                {{ form.priority ? `Semaforo ${form.priority}` : 'Sin semaforo' }}
                            </span>
                        </div>
                        <InputError :message="form.errors.priority" />
                    </div>

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
                                :disabled="form.is_completed"
                                class="h-10 w-full rounded-md border border-input bg-background py-2 pr-3 pl-10 text-sm disabled:cursor-not-allowed disabled:opacity-60"
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
                </section>

                <section
                    class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-5"
                >
                    <div class="flex items-start gap-4">
                        <Checkbox
                            id="is_completed"
                            :model-value="form.is_completed"
                            class="mt-1"
                            @update:model-value="form.is_completed = Boolean($event)"
                        />

                        <div class="space-y-2">
                            <Label
                                for="is_completed"
                                class="flex cursor-pointer items-center gap-2 text-sm font-semibold text-foreground"
                            >
                                <CircleCheckBig class="size-4 text-emerald-600" />
                                Marcar como completada
                            </Label>
                            <p class="text-sm text-muted-foreground">
                                Activalo para cerrar la tarea rapido. El sistema
                                registrara la fecha de completado automaticamente.
                            </p>
                        </div>
                    </div>
                    <InputError :message="form.errors.is_completed" />
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
