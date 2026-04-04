<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { CalendarDays, CreditCard, FileText, FolderKanban } from 'lucide-vue-next';
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
    ProjectPaymentFormData,
    ProjectPaymentProjectOption,
} from '@/modules/projectPayments/types';

defineProps<{
    form: InertiaForm<ProjectPaymentFormData>;
    projects: ProjectPaymentProjectOption[];
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
            <CardTitle>Registro de pago</CardTitle>
            <CardDescription>
                {{ description }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form class="space-y-6" @submit.prevent="$emit('submit')">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            for="project_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Proyecto
                        </Label>
                        <div class="relative">
                            <FolderKanban
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <select
                                id="project_id"
                                v-model="form.project_id"
                                class="h-10 w-full rounded-md border border-input bg-background py-2 pr-3 pl-10 text-sm"
                            >
                                <option value="">Selecciona un proyecto</option>
                                <option
                                    v-for="project in projects"
                                    :key="project.id"
                                    :value="String(project.id)"
                                >
                                    {{ project.label }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="form.errors.project_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="amount"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Monto
                        </Label>
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-sm font-semibold text-muted-foreground"
                            >
                                S/
                            </span>
                            <Input
                                id="amount"
                                v-model="form.amount"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.amount" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="payment_date"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Fecha de pago
                        </Label>
                        <div class="relative">
                            <CalendarDays
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="payment_date"
                                v-model="form.payment_date"
                                type="date"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.payment_date" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            for="payment_method"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Metodo de pago
                        </Label>
                        <div class="relative">
                            <CreditCard
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="payment_method"
                                v-model="form.payment_method"
                                type="text"
                                placeholder="Ej. Transferencia bancaria"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.payment_method" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            for="notes"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Notas
                        </Label>
                        <div class="relative">
                            <FileText
                                class="pointer-events-none absolute top-4 left-3 size-4 text-muted-foreground"
                            />
                            <Textarea
                                id="notes"
                                v-model="form.notes"
                                rows="4"
                                placeholder="Detalles adicionales sobre la transaccion..."
                                class="pl-10"
                            />
                        </div>
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
