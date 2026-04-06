<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    CalendarDays,
    Check,
    ChevronsUpDown,
    FileText,
    FolderKanban,
} from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
    ComboboxViewport,
} from '@/components/ui/combobox';
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
    ProjectClientOption,
    ProjectFormData,
    ProjectStatusOption,
} from '@/modules/projects/types';

const props = defineProps<{
    form: InertiaForm<ProjectFormData>;
    clients: ProjectClientOption[];
    statusOptions: ProjectStatusOption[];
    submitLabel: string;
    processingLabel: string;
    cancelHref: string;
    description: string;
}>();

defineEmits<{
    (e: 'submit'): void;
}>();

const clientOptions = computed(() => [
    ...props.clients.map((client) => ({
        id: String(client.id),
        label: client.label,
    })),
]);

const selectedClient = computed({
    get: () =>
        clientOptions.value.find(
            (client) => client.id === props.form.client_id,
        ),
    set: (client?: { id: string; label: string }) => {
        props.form.client_id = client?.id ?? '';
    },
});
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Configuracion del proyecto</CardTitle>
            <CardDescription>
                {{ description }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form class="space-y-6" @submit.prevent="$emit('submit')">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            for="name"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Nombre del proyecto
                        </Label>
                        <div class="relative">
                            <FolderKanban
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Ej. Rediseño web corporativo"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="client_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Cliente asignado
                        </Label>
                        <Combobox v-model="selectedClient" by="id">
                            <ComboboxAnchor as-child class="w-full">
                                <ComboboxTrigger as-child>
                                    <Button
                                        variant="outline"
                                        class="h-10 w-full justify-between font-normal"
                                    >
                                        {{
                                            selectedClient?.label ??
                                            'Seleccionar'
                                        }}
                                        <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
                                    </Button>
                                </ComboboxTrigger>
                            </ComboboxAnchor>
                            <ComboboxList class="w-[var(--reka-combobox-trigger-width)] overflow-hidden p-0">
                                <div class="border-b px-3">
                                    <ComboboxInput
                                        class="h-9 border-0 px-0 shadow-none focus-visible:ring-0"
                                        placeholder="Buscar cliente..."
                                        :display-value="(value) => value?.label ?? ''"
                                    />
                                </div>
                                <ComboboxEmpty>No se encontraron clientes.</ComboboxEmpty>
                                <ComboboxViewport>
                                    <ComboboxGroup>
                                        <ComboboxItem
                                            v-for="client in clientOptions"
                                            :key="client.id"
                                            :value="client"
                                        >
                                            {{ client.label }}
                                            <ComboboxItemIndicator>
                                                <Check class="size-4" />
                                            </ComboboxItemIndicator>
                                        </ComboboxItem>
                                    </ComboboxGroup>
                                </ComboboxViewport>
                            </ComboboxList>
                        </Combobox>
                        <InputError :message="form.errors.client_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="price"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Monto total
                        </Label>
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-sm font-semibold text-muted-foreground"
                            >
                                S/
                            </span>
                            <Input
                                id="price"
                                v-model="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.price" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Estado inicial
                        </Label>
                        <div class="flex flex-wrap gap-3">
                            <label
                                v-for="status in statusOptions"
                                :key="status.value"
                                class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium transition-colors"
                                :class="
                                    form.status === status.value
                                        ? 'border-primary/30 bg-primary/10 text-primary'
                                        : 'border-border/60 bg-background text-muted-foreground hover:bg-muted'
                                "
                            >
                                <input
                                    v-model="form.status"
                                    type="radio"
                                    name="status"
                                    :value="status.value"
                                    class="size-4 text-primary"
                                />
                                <span>{{ status.label }}</span>
                            </label>
                        </div>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="start_date"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Fecha de inicio
                        </Label>
                        <div class="relative">
                            <CalendarDays
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.start_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="due_date"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Fecha estimada de cierre
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

                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            for="description"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Descripcion y alcance
                        </Label>
                        <div class="relative">
                            <FileText
                                class="pointer-events-none absolute top-4 left-3 size-4 text-muted-foreground"
                            />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Describe el alcance, objetivos y entregables principales..."
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            for="notes"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Notas internas
                        </Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            placeholder="Contexto interno, observaciones o acuerdos relevantes..."
                        />
                        <InputError :message="form.errors.notes" />
                    </div>
                </div>

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
