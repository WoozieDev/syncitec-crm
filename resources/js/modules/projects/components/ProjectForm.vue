<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { CalendarDays, FileText, FolderKanban } from 'lucide-vue-next';
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
    ProjectClientOption,
    ProjectFormData,
    ProjectStatusOption,
} from '@/modules/projects/types';

defineProps<{
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
                        <select
                            id="client_id"
                            v-model="form.client_id"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Selecciona un cliente</option>
                            <option
                                v-for="client in clients"
                                :key="client.id"
                                :value="String(client.id)"
                            >
                                {{ client.label }}
                            </option>
                        </select>
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
