<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';

const { title = '', description = '', variant = 'default' } = defineProps<{
    title?: string;
    description?: string;
    variant?: 'default' | 'login';
}>();
</script>

<template>
    <AuthLayout
        :title="title"
        :description="description"
        :variant="variant"
    >
        <slot />
    </AuthLayout>
</template>
