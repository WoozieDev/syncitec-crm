<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import {
    CalendarDays,
    Check,
    ChevronsUpDown,
    CircleDollarSign,
    FileText,
    Layers3,
    StickyNote,
    Wrench,
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
    ProviderOption,
    ServiceClientOption,
    ServiceFormData,
    ServiceSelectOption,
    ServiceTypeOption,
} from '@/modules/services/types';

const props = defineProps<{
    form: InertiaForm<ServiceFormData>;
    clients: ServiceClientOption[];
    serviceTypes: ServiceTypeOption[];
    providers: ProviderOption[];
    billingTypeOptions: ServiceSelectOption[];
    billingCycleOptions: ServiceSelectOption[];
    statusOptions: ServiceSelectOption[];
    submitLabel: string;
    processingLabel: string;
    cancelHref: string;
    description: string;
}>();

defineEmits<{
    (e: 'submit'): void;
}>();

watch(
    () => props.form.billing_type,
    (billingType) => {
        if (billingType !== 'recurrente') {
            props.form.billing_cycle = '';
        }
    },
);

const selectedClientLabel = computed(() => {
    const selectedClient = props.clients.find(
        (client) => String(client.id) === props.form.client_id,
    );

    return selectedClient?.label ?? 'Sin cliente seleccionado';
});

const selectedTypeLabel = computed(() => {
    const selectedType = props.serviceTypes.find(
        (serviceType) => String(serviceType.id) === props.form.service_type_id,
    );

    return selectedType?.label ?? 'Sin tipo seleccionado';
});

const selectedProviderLabel = computed(() => {
    if (!props.form.provider_id) {
        return 'Sin proveedor';
    }

    const selectedProvider = props.providers.find(
        (provider) => String(provider.id) === props.form.provider_id,
    );

    return selectedProvider?.label ?? 'Sin proveedor';
});

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
    <form class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_340px]" @submit.prevent="$emit('submit')">
        <div class="space-y-6">
            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Informacion general</CardTitle>
                    <CardDescription>
                        {{ description }}
                    </CardDescription>
                </CardHeader>

                <CardContent class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label
                            for="client_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Cliente
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
                            for="name"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Nombre del servicio
                        </Label>
                        <div class="relative">
                            <Wrench
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Ej. Mantenimiento web mensual"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="service_type_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Tipo de servicio
                        </Label>
                        <div class="relative">
                            <Layers3
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <select
                                id="service_type_id"
                                v-model="form.service_type_id"
                                class="h-10 w-full rounded-md border border-input bg-background py-2 pr-3 pl-10 text-sm"
                            >
                                <option value="">Selecciona un tipo</option>
                                <option
                                    v-for="serviceType in serviceTypes"
                                    :key="serviceType.id"
                                    :value="String(serviceType.id)"
                                >
                                    {{ serviceType.label }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="form.errors.service_type_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="provider_id"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Proveedor
                        </Label>
                        <select
                            id="provider_id"
                            v-model="form.provider_id"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Sin proveedor</option>
                            <option
                                v-for="provider in providers"
                                :key="provider.id"
                                :value="String(provider.id)"
                            >
                                {{ provider.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.provider_id" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label
                            for="description"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Descripcion
                        </Label>
                        <div class="relative">
                            <FileText
                                class="pointer-events-none absolute top-4 left-3 size-4 text-muted-foreground"
                            />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Describe el alcance y el contexto operativo del servicio..."
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.description" />
                    </div>
                </CardContent>
            </Card>

            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Facturacion</CardTitle>
                    <CardDescription>
                        Define el esquema comercial real del servicio.
                    </CardDescription>
                </CardHeader>

                <CardContent class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label
                            for="billing_type"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Tipo de facturacion
                        </Label>
                        <select
                            id="billing_type"
                            v-model="form.billing_type"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Selecciona un tipo</option>
                            <option
                                v-for="option in billingTypeOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.billing_type" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="billing_cycle"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Ciclo de facturacion
                        </Label>
                        <select
                            id="billing_cycle"
                            v-model="form.billing_cycle"
                            :disabled="form.billing_type !== 'recurrente'"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <option value="">
                                {{
                                    form.billing_type === 'recurrente'
                                        ? 'Selecciona un ciclo'
                                        : 'No aplica'
                                }}
                            </option>
                            <option
                                v-for="option in billingCycleOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.billing_cycle" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="price"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Precio
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

                    <div class="grid gap-2">
                        <Label
                            for="cost"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Costo
                        </Label>
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-sm font-semibold text-muted-foreground"
                            >
                                S/
                            </span>
                            <Input
                                id="cost"
                                v-model="form.cost"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.cost" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="space-y-6">
            <Card class="border-border/60 bg-muted/30">
                <CardHeader>
                    <CardTitle>Renovacion</CardTitle>
                    <CardDescription>
                        Fechas operativas y estado vigente del servicio.
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-6">
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
                            for="next_renewal_date"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Proxima renovacion
                        </Label>
                        <div class="relative">
                            <CalendarDays
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="next_renewal_date"
                                v-model="form.next_renewal_date"
                                type="date"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="form.errors.next_renewal_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="status"
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Estado
                        </Label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Selecciona un estado</option>
                            <option
                                v-for="option in statusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.status" />
                    </div>
                </CardContent>
            </Card>

            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Notas</CardTitle>
                    <CardDescription>
                        Observaciones internas relacionadas al servicio.
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-4">
                    <div class="relative">
                        <StickyNote
                            class="pointer-events-none absolute top-4 left-3 size-4 text-muted-foreground"
                        />
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            rows="6"
                            placeholder="Contexto interno, acuerdos o alertas operativas..."
                            class="pl-10"
                        />
                    </div>
                    <InputError :message="form.errors.notes" />
                </CardContent>
            </Card>

            <Card class="overflow-hidden border-blue-400/20 bg-linear-to-br from-blue-600 via-blue-700 to-slate-950 text-white shadow-xl shadow-blue-950/15">
                <CardContent class="space-y-5 p-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex size-11 items-center justify-center rounded-2xl bg-white/10"
                        >
                            <CircleDollarSign class="size-5" />
                        </div>
                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.2em] text-white/65 uppercase"
                            >
                                Resumen
                            </p>
                            <p class="text-sm text-white/80">
                                {{ selectedClientLabel }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Tipo</span>
                            <span class="font-semibold text-white">
                                {{ selectedTypeLabel }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Proveedor</span>
                            <span class="font-semibold text-white">
                                {{ selectedProviderLabel }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-white/65">Estado</span>
                            <span class="font-semibold text-white">
                                {{
                                    statusOptions.find(
                                        (option) => option.value === form.status,
                                    )?.label ?? 'Sin definir'
                                }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-white/10 pt-5">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full cursor-pointer bg-white text-primary hover:bg-white/90"
                        >
                            {{
                                form.processing ? processingLabel : submitLabel
                            }}
                        </Button>

                        <Link :href="cancelHref" class="w-full">
                            <Button
                                type="button"
                                variant="outline"
                                class="w-full cursor-pointer border-white/15 bg-transparent text-white hover:bg-white/10 hover:text-white"
                            >
                                Cancelar
                            </Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    </form>
</template>
