<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import AuthDecorativeBackground from '@/modules/auth/components/AuthDecorativeBackground.vue';
import { home } from '@/routes';

const props = defineProps<{
    title?: string;
    description?: string;
    variant?: 'default' | 'login';
}>();

const isLoginVariant = computed(() => props.variant === 'login');
</script>

<template>
    <div
        v-if="isLoginVariant"
        class="relative min-h-svh overflow-hidden bg-background px-4 py-6 sm:px-6 sm:py-8"
    >
        <AuthDecorativeBackground />

        <div
            class="relative mx-auto flex min-h-[calc(100svh-3rem)] max-w-5xl items-center justify-center"
        >
            <div class="w-full">
                <slot />
            </div>
        </div>
    </div>

    <div
        v-else
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex h-9 w-9 items-center justify-center rounded-md"
                        >
                            <AppLogoIcon
                                class="size-9 fill-current text-foreground dark:text-white"
                            />
                        </div>
                        <span class="sr-only">{{ props.title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ props.title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ props.description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
