<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LockKeyhole } from 'lucide-vue-next';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthField from '@/modules/auth/components/AuthField.vue';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Confirmar contrasena',
        description:
            'Esta es un area segura de la aplicacion. Confirma tu contrasena para continuar.',
    },
});
</script>

<template>
    <Head title="Confirmar contrasena" />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
        class="space-y-6"
    >
        <div class="space-y-6">
            <AuthField
                label="Contrasena"
                for-id="password"
                :icon="LockKeyhole"
                :error="errors.password"
            >
                <PasswordInput
                    id="password"
                    name="password"
                    class="h-14 rounded-xl border-transparent bg-muted/55 pr-12 pl-12 shadow-none focus-visible:bg-background dark:bg-muted/35"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="Ingresa tu contrasena"
                />
            </AuthField>

            <div class="flex items-center">
                <Button
                    class="h-14 w-full cursor-pointer rounded-xl text-sm font-semibold shadow-lg shadow-primary/25"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <Spinner v-if="processing" />
                    Confirmar contrasena
                </Button>
            </div>
        </div>
    </Form>
</template>
