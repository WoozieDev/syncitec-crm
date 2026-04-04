<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { CalendarDays, CreditCard, FileText, Wrench } from 'lucide-vue-next';
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
    ServicePaymentFormData,
    ServicePaymentServiceOption,
} from '@/modules/servicePayments/types';

defineProps<{
    form: InertiaForm<ServicePaymentFormData>;
    services: ServicePaymentServiceOption[];
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
    <Card class="border-border/60">
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
                            for="service_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Servicio
                        </Label>
                        <div class="relative">
                            <Wrench
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <select
                                id="service_id"
                                v-model="form.service_id"
                                class="h-11 w-full rounded-full border border-input bg-background py-2 pr-3 pl-10 text-sm"
                            >
                                <option value="">Selecciona un servicio</option>
                                <option
                                    v-for="service in services"
                                    :key="service.id"
                                    :value="String(service.id)"
                                >
                                    {{ service.label }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="form.errors.service_id" />
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
                                class="rounded-full pl-10"
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
                                class="rounded-full pl-10"
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
                                class="rounded-full pl-10"
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
                                placeholder="Detalles adicionales sobre el pago..."
                                class="rounded-2xl pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.notes" />
                    </div>
                </div>

                <div
                    class="flex flex-wrap items-center justify-end gap-3 border-t pt-6"
                >
                    <Link :href="cancelHref">
                        <Button
                            type="button"
                            variant="outline"
                            class="cursor-pointer rounded-full"
                        >
                            Cancelar
                        </Button>
                    </Link>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="cursor-pointer rounded-full"
                    >
                        {{ form.processing ? processingLabel : submitLabel }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
