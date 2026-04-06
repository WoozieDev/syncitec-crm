<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Verificar correo',
        description:
            'Verifica tu correo haciendo clic en el enlace que acabamos de enviarte.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Verificacion de correo" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-6 rounded-2xl border border-primary/20 bg-primary/10 px-4 py-3 text-center text-sm text-primary"
    >
        Enviamos un nuevo enlace de verificacion al correo registrado.
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button
            :disabled="processing"
            class="h-14 w-full cursor-pointer rounded-xl text-sm font-semibold shadow-lg shadow-primary/25"
        >
            <Spinner v-if="processing" />
            Reenviar correo de verificacion
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            Cerrar sesion
        </TextLink>
    </Form>
</template>
