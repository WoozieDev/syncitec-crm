<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Building2, Mail, Phone, UserRound } from 'lucide-vue-next';

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
import { Textarea } from '@/components/ui/textarea';
import ClientRecommendationsCard from '@/modules/clients/components/ClientRecommendationsCard.vue';
import type { Client, ClientFormData } from '@/modules/clients/types';
import { index, update } from '@/routes/clients';

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
    company: props.client.company ?? '',
    email: props.client.email,
    phone: props.client.phone ?? '',
    country: props.client.country ?? '',
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

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar cliente"
                description="Actualiza la informacion y manten al dia la ficha del cliente."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
                <Card>
                    <CardHeader>
                        <CardTitle>Informacion del cliente</CardTitle>
                        <CardDescription>
                            Edita los datos principales para reflejar el estado
                            actual del cliente.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form class="space-y-6" @submit.prevent="submit">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label
                                        for="name"
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Nombre completo
                                    </Label>
                                    <div class="relative">
                                        <UserRound
                                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <Input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            placeholder="Ej. Jonathan Ive"
                                            autocomplete="name"
                                            class="pl-10"
                                        />
                                    </div>
                                    <InputError :message="form.errors.name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        for="company"
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Empresa
                                    </Label>
                                    <div class="relative">
                                        <Building2
                                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <Input
                                            id="company"
                                            v-model="form.company"
                                            type="text"
                                            placeholder="Ej. Acme Corp"
                                            autocomplete="organization"
                                            class="pl-10"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.company"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        for="email"
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Correo
                                    </Label>
                                    <div class="relative">
                                        <Mail
                                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <Input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            placeholder="correo@empresa.com"
                                            autocomplete="email"
                                            class="pl-10"
                                        />
                                    </div>
                                    <InputError :message="form.errors.email" />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        for="phone"
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Telefono
                                    </Label>
                                    <div class="relative">
                                        <Phone
                                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <Input
                                            id="phone"
                                            v-model="form.phone"
                                            type="text"
                                            placeholder="+1 (555) 000-0000"
                                            autocomplete="tel"
                                            class="pl-10"
                                        />
                                    </div>
                                    <InputError :message="form.errors.phone" />
                                </div>

                                <div class="grid gap-2 md:col-span-2">
                                    <Label
                                        for="country"
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Pais
                                    </Label>
                                    <Input
                                        id="country"
                                        v-model="form.country"
                                        type="text"
                                        placeholder="Ej. Peru"
                                        autocomplete="country-name"
                                    />
                                    <InputError
                                        :message="form.errors.country"
                                    />
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
                                        rows="4"
                                        placeholder="Contexto comercial, acuerdos o consideraciones del cliente..."
                                    />
                                    <InputError :message="form.errors.notes" />
                                </div>
                            </div>

                            <div
                                class="flex flex-wrap items-center justify-end gap-3 border-t pt-6"
                            >
                                <Link :href="index()">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        class="cursor-pointer"
                                    >
                                        Cancelar
                                    </Button>
                                </Link>
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
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <ClientRecommendationsCard />
            </div>
        </div>
    </div>
</template>
