<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
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

import type { Client, ClientFormData } from '@/modules/clients/types';
import { index, update } from '@/routes/clients';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{
    client: Client;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes',
                href: index(),
            },
            {
                title: 'Editar cliente',
                href: '#',
            },
        ],
    },
});

const form = useForm<ClientFormData>({
    name: props.client.name,
    company: props.client.company,
    email: props.client.email,
    phone: props.client.phone,
    country: props.client.country,
    notes: props.client.notes ?? '',
});

const submit = () => {
    form.submit(update(props.client.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar cliente - ${client.name}`" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar cliente"
                description="Actualiza la información del cliente."
            />

            <Card>
                <CardHeader>
                    <CardTitle>Información del cliente</CardTitle>
                    <CardDescription>
                        Modifica los datos principales del cliente.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Nombre</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Nombre del cliente"
                                    autocomplete="name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="company">Empresa</Label>
                                <Input
                                    id="company"
                                    v-model="form.company"
                                    type="text"
                                    placeholder="Nombre de la empresa"
                                    autocomplete="organization"
                                />
                                <InputError :message="form.errors.company" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="correo@empresa.com"
                                    autocomplete="email"
                                />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="phone">Teléfono</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    type="text"
                                    placeholder="999999999"
                                    autocomplete="tel"
                                />
                                <InputError :message="form.errors.phone" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="country">País</Label>
                                <Input
                                    id="country"
                                    v-model="form.country"
                                    type="text"
                                    placeholder="Perú"
                                    autocomplete="country-name"
                                />
                                <InputError :message="form.errors.country" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="notes">Notas</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    placeholder="Notas internas del cliente"
                                    rows="4"
                                />
                                <InputError :message="form.errors.notes" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="cursor-pointer"
                            >
                                {{
                                    form.processing
                                        ? 'Guardando...'
                                        : 'Actualizar cliente'
                                }}
                            </Button>

                            <Link :href="index()">
                                <Button
                                    type="button"
                                    variant="outline"
                                    class="cursor-pointer"
                                >
                                    Cancelar
                                </Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
