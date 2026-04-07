<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ArrowRight, LayoutGrid, LockKeyhole, Mail } from 'lucide-vue-next';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthField from '@/modules/auth/components/AuthField.vue';
import { store } from '@/routes/login';
import { request as passwordRequest } from '@/routes/password';

defineOptions({
    layout: {
        title: 'CRM Woozie',
        description: 'Accede a tu espacio de trabajo',
        variant: 'login',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sesion" />

    <div class="mx-auto flex w-full max-w-md flex-col items-center">
        <div class="space-y-5 text-center">
            <div
                class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary/12 text-primary shadow-sm ring-1 ring-primary/10"
            >
                <LayoutGrid class="size-7" />
            </div>

            <div class="space-y-2">
                <h1 class="text-4xl font-black tracking-tight text-foreground">
                    CRM Woozie
                </h1>
            </div>
        </div>

        <div class="mt-10 w-full">
            <Card
                class="rounded-4xl border-border/40 bg-card/92 p-8 shadow-[0_22px_60px_-28px_rgba(59,130,246,0.35)] backdrop-blur dark:border-border/70 dark:bg-card/88 dark:shadow-[0_24px_70px_-32px_rgba(2,6,23,0.82)]"
            >
                <div class="space-y-7">
                    <div
                        v-if="status"
                        class="rounded-2xl border border-primary/20 bg-primary/10 px-4 py-3 text-sm text-primary"
                    >
                        {{ status }}
                    </div>

                    <Form
                        v-bind="store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <AuthField
                            label="Correo electronico"
                            for-id="email"
                            :icon="Mail"
                            :error="errors.email"
                        >
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="nombre@empresa.com"
                                class="h-14 rounded-xl border-transparent bg-muted/55 pl-12 shadow-none focus-visible:bg-background dark:bg-muted/35"
                            />
                        </AuthField>

                        <AuthField
                            label="Contraseña"
                            for-id="password"
                            :icon="LockKeyhole"
                            :error="errors.password"
                        >
                            <template v-if="canResetPassword" #action>
                                <TextLink
                                    :href="passwordRequest()"
                                    class="text-xs font-semibold normal-case tracking-normal"
                                >
                                    Recuperar contraseña
                                </TextLink>
                            </template>

                            <PasswordInput
                                id="password"
                                name="password"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                placeholder="Ingresa tu contraseña"
                                class="h-14 rounded-xl border-transparent bg-muted/55 pr-12 pl-12 shadow-none focus-visible:bg-background dark:bg-muted/35"
                            />
                        </AuthField>

                        <div
                            class="flex items-center justify-between gap-4 pt-1"
                        >
                            <Label
                                for="remember"
                                class="flex items-center gap-3 text-sm font-medium text-muted-foreground"
                            >
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                />
                                <span>Recordar este dispositivo</span>
                            </Label>
                        </div>

                        <Button
                            type="submit"
                            class="h-14 w-full cursor-pointer rounded-xl text-sm font-semibold shadow-lg shadow-primary/25"
                            :tabindex="4"
                            :disabled="processing"
                            data-test="login-button"
                        >
                            <Spinner v-if="processing" />
                            <span>
                                {{
                                    processing
                                        ? 'Ingresando...'
                                        : 'Ingresar al espacio de trabajo'
                                }}
                            </span>
                            <ArrowRight class="size-4" />
                        </Button>
                    </Form>
                </div>
            </Card>
        </div>
    </div>
</template>
