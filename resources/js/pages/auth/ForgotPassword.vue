<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail } from 'lucide-vue-next';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import AuthField from '@/modules/auth/components/AuthField.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Recuperar contrasena',
        description:
            'Ingresa tu correo para enviarte un enlace de restablecimiento.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Recuperar contrasena" />

    <div
        v-if="status"
        class="mb-6 rounded-2xl border border-primary/20 bg-primary/10 px-4 py-3 text-center text-sm text-primary"
    >
        {{ status }}
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }" class="space-y-6">
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
                    autocomplete="off"
                    autofocus
                    placeholder="nombre@empresa.com"
                    class="h-14 rounded-xl border-transparent bg-muted/55 pl-12 shadow-none focus-visible:bg-background dark:bg-muted/35"
                />
            </AuthField>

            <div class="flex items-center justify-start">
                <Button
                    class="h-14 w-full cursor-pointer rounded-xl text-sm font-semibold shadow-lg shadow-primary/25"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <Spinner v-if="processing" />
                    Enviar enlace de recuperacion
                </Button>
            </div>
        </Form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>Volver a</span>
            <TextLink :href="login()">iniciar sesion</TextLink>
        </div>
    </div>
</template>
