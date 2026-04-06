<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { Mail, UserRound } from 'lucide-vue-next';

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
import type { ProfileUser } from '@/modules/profile/types';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { send } from '@/routes/verification';

type Props = {
    user: ProfileUser;
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Informacion de mi perfil</CardTitle>
            <CardDescription>
                Actualiza tus datos principales para mantener tu cuenta vigente.
            </CardDescription>
        </CardHeader>

        <CardContent>
            <Form
                v-bind="ProfileController.update.form()"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
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
                                class="pl-10"
                                name="name"
                                :default-value="user.name"
                                required
                                autocomplete="name"
                                placeholder="Ej. Maria Rodriguez"
                            />
                        </div>

                        <InputError :message="errors.name" />
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
                                type="email"
                                class="pl-10"
                                name="email"
                                :default-value="user.email"
                                required
                                autocomplete="username"
                                placeholder="nombre@empresa.com"
                            />
                        </div>

                        <InputError :message="errors.email" />
                    </div>
                </div>

                <div
                    v-if="mustVerifyEmail && !user.email_verified_at"
                    class="rounded-xl border border-amber-300/50 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200"
                >
                    <p>
                        Tu correo no está verificado.
                        <Link
                            :href="send()"
                            as="button"
                            class="font-semibold underline underline-offset-4"
                        >
                            Reenviar correo de verificacion.
                        </Link>
                    </p>

                    <p
                        v-if="status === 'verification-link-sent'"
                        class="mt-2 font-medium text-green-700 dark:text-green-300"
                    >
                        Se envio un nuevo enlace de verificacion.
                    </p>
                </div>

                <div
                    class="flex flex-wrap items-center justify-end gap-3 border-t pt-6"
                >
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-show="recentlySuccessful"
                            class="text-sm text-neutral-600"
                        >
                            Guardado correctamente.
                        </p>
                    </Transition>

                    <Button :disabled="processing" class="cursor-pointer">
                        {{ processing ? 'Guardando...' : 'Actualizar perfil' }}
                    </Button>
                </div>
            </Form>
        </CardContent>
    </Card>
</template>
