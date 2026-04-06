<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LockKeyhole, Mail } from 'lucide-vue-next';
import { ref } from 'vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import AuthField from '@/modules/auth/components/AuthField.vue';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Restablecer contrasena',
        description: 'Ingresa tu nueva contrasena para recuperar el acceso.',
    },
});

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Restablecer contrasena" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="space-y-6"
    >
        <div class="space-y-6">
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
                    autocomplete="email"
                    v-model="inputEmail"
                    class="h-14 rounded-xl border-transparent bg-muted/55 pl-12 shadow-none focus-visible:bg-background dark:bg-muted/35"
                    readonly
                />
            </AuthField>

            <AuthField
                label="Nueva contrasena"
                for-id="password"
                :icon="LockKeyhole"
                :error="errors.password"
            >
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    class="h-14 rounded-xl border-transparent bg-muted/55 pr-12 pl-12 shadow-none focus-visible:bg-background dark:bg-muted/35"
                    autofocus
                    placeholder="Nueva contrasena"
                />
            </AuthField>

            <AuthField
                label="Confirmar contrasena"
                for-id="password_confirmation"
                :icon="LockKeyhole"
                :error="errors.password_confirmation"
            >
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="h-14 rounded-xl border-transparent bg-muted/55 pr-12 pl-12 shadow-none focus-visible:bg-background dark:bg-muted/35"
                    placeholder="Confirmar contrasena"
                />
            </AuthField>

            <Button
                type="submit"
                class="h-14 w-full cursor-pointer rounded-xl text-sm font-semibold shadow-lg shadow-primary/25"
                :disabled="processing"
                data-test="reset-password-button"
            >
                <Spinner v-if="processing" />
                Restablecer contrasena
            </Button>
        </div>
    </Form>
</template>
