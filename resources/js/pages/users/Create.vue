<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LockKeyhole, Mail, UserRound } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
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
import UserRecommendationsCard from '@/modules/users/components/UserRecommendationsCard.vue';
import type { UserFormData } from '@/modules/users/types';
import { create, index, store } from '@/routes/users';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: index(),
            },
            {
                title: 'Nuevo usuario',
                href: create(),
            },
        ],
    },
});

const form = useForm<UserFormData>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo usuario" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nuevo usuario"
                description="Crea una cuenta interna con acceso al CRM y configura sus credenciales iniciales."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
                <Card>
                    <CardHeader>
                        <CardTitle>Informacion de acceso</CardTitle>
                        <CardDescription>
                            Completa los datos basicos y define una clave segura
                            para el nuevo usuario.
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
                                            placeholder="Ej. Ana Torres"
                                            autocomplete="name"
                                            class="pl-10"
                                        />
                                    </div>
                                    <InputError :message="form.errors.name" />
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
                                            placeholder="ana@empresa.com"
                                            autocomplete="email"
                                            class="pl-10"
                                        />
                                    </div>
                                    <InputError :message="form.errors.email" />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        for="password"
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Contraseña
                                    </Label>
                                    <div class="relative">
                                        <LockKeyhole
                                            class="pointer-events-none absolute top-1/2 left-3 z-10 size-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <PasswordInput
                                            id="password"
                                            v-model="form.password"
                                            placeholder="Minimo 8 caracteres"
                                            autocomplete="new-password"
                                            class="pl-10"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.password"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        for="password_confirmation"
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Confirmar contraseña
                                    </Label>
                                    <PasswordInput
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        placeholder="Repite la contraseña"
                                        autocomplete="new-password"
                                    />
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
                                            : 'Crear usuario'
                                    }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <UserRecommendationsCard />
            </div>
        </div>
    </div>
</template>
