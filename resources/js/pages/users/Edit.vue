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
import type { UserFormData, UserRecord } from '@/modules/users/types';
import { index, update } from '@/routes/users';

const props = defineProps<{
    user: UserRecord;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: index(),
            },
            {
                title: 'Editar usuario',
                href: '#',
            },
        ],
    },
});

const form = useForm<UserFormData>({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.submit(update(props.user.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar usuario - ${user.name}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar usuario"
                description="Actualiza los datos de acceso y cambia la contraseña solo cuando sea necesario."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
                <Card>
                    <CardHeader>
                        <CardTitle>Informacion de acceso</CardTitle>
                        <CardDescription>
                            Mantiene el perfil al dia. Si no deseas cambiar la
                            clave, deja los campos de contraseña en blanco.
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
                                        Nueva contraseña
                                    </Label>
                                    <div class="relative">
                                        <LockKeyhole
                                            class="pointer-events-none absolute top-1/2 left-3 z-10 size-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <PasswordInput
                                            id="password"
                                            v-model="form.password"
                                            placeholder="Dejar vacio para conservar la actual"
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
                                        placeholder="Repite la nueva contraseña"
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
                                            : 'Actualizar usuario'
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
