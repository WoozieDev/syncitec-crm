<script setup lang="ts">
import type { Component } from 'vue';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';

defineProps<{
    label: string;
    forId: string;
    error?: string;
    icon?: Component;
}>();
</script>

<template>
    <div class="space-y-2.5">
        <div class="flex items-center justify-between gap-3">
            <Label
                :for="forId"
                class="text-xs font-semibold uppercase tracking-[0.24em] text-muted-foreground"
            >
                {{ label }}
            </Label>

            <slot name="action" />
        </div>

        <div class="relative">
            <component
                :is="icon"
                v-if="icon"
                class="pointer-events-none absolute top-1/2 left-4 z-10 size-4 -translate-y-1/2 text-muted-foreground"
            />

            <slot />
        </div>

        <InputError :message="error" />
    </div>
</template>
