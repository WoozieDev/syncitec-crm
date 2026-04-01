<script setup lang="ts">
import WorkspaceTopbar from '@/components/WorkspaceTopbar.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <WorkspaceTopbar />
</template>
