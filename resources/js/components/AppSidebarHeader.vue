<script setup lang="ts">
import WorkspaceTopbar from '@/components/WorkspaceTopbar.vue';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <WorkspaceTopbar show-sidebar-trigger />
</template>
