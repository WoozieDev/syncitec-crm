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
import type {
    ProjectKanbanColumn,
    ProjectKanbanColumnFormData,
    ProjectKanbanProject,
} from '@/modules/projectKanban/types';

const props = defineProps<{
    open: boolean;
    mode: 'create' | 'edit';
    project: ProjectKanbanProject;
    column: ProjectKanbanColumn | null;
    currentView: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'saved'): void;
}>();

const form = useForm<ProjectKanbanColumnFormData>({
    name: '',
    order: '0',
});

const dialogTitle = computed(() =>
    props.mode === 'create' ? 'Nueva columna' : 'Editar columna',
);

const dialogDescription = computed(() =>
    props.mode === 'create'
        ? 'Crea una nueva columna real del proyecto. Las tareas viviran dentro de ella.'
        : 'Ajusta el nombre o el orden de la columna seleccionada.',
);

const requestUrl = computed(() => {
    if (props.mode === 'edit' && props.column?.id) {
        return `/projects/${props.project.id}/modules/${props.column.id}`;
    }

    return `/projects/${props.project.id}/modules`;
});

const syncForm = () => {
    if (props.mode === 'create') {
        form.defaults({
            name: '',
            order: String(props.project.modules.length + 1),
        });
    } else {
        form.defaults({
            name: props.column?.name ?? '',
            order: String(props.column?.order ?? 0),
        });
    }

    form.reset();
    form.clearErrors();
};

watch(
    () => [props.open, props.mode, props.column?.id, props.project.modules.length],
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
        form.post(`${requestUrl.value}?view=${props.currentView}`, options);

        return;
    }

    form.put(`${requestUrl.value}?view=${props.currentView}`, options);
};
</script>

<template>
    <Dialog :open="open" @update:open="(value) => !value && close()">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ dialogTitle }}</DialogTitle>
                <DialogDescription>
                    {{ dialogDescription }}
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="column-name">Nombre</Label>
                    <Input
                        id="column-name"
                        v-model="form.name"
                        type="text"
                        placeholder="Ej. Produccion, Revision, Entrega"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="column-order">Orden</Label>
                    <Input
                        id="column-order"
                        v-model="form.order"
                        type="number"
                        min="0"
                        step="1"
                    />
                    <InputError :message="form.errors.order" />
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
                                  ? 'Crear columna'
                                  : 'Actualizar columna'
                        }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
