<script setup lang="ts">
import { LayoutGrid } from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import AuthDecorativeBackground from '@/modules/auth/components/AuthDecorativeBackground.vue';

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
        class="relative min-h-svh overflow-hidden bg-background px-4 py-6 sm:px-6 sm:py-8"
    >
        <AuthDecorativeBackground />

        <div
            class="relative mx-auto flex min-h-[calc(100svh-3rem)] max-w-5xl items-center justify-center"
        >
            <div class="w-full max-w-md">
                <div class="space-y-8">
                    <div class="space-y-5 text-center">
                        <div
                            class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary/12 text-primary shadow-sm ring-1 ring-primary/10"
                        >
                            <LayoutGrid class="size-7" />
                        </div>

                        <div class="space-y-2">
                            <h1
                                class="text-3xl font-black tracking-tight text-foreground"
                            >
                                {{ props.title }}
                            </h1>

                            <p
                                v-if="props.description"
                                class="mx-auto max-w-sm text-sm leading-6 text-muted-foreground"
                            >
                                {{ props.description }}
                            </p>
                        </div>
                    </div>

                    <Card
                        class="rounded-4xl border-border/40 bg-card/92 p-8 shadow-[0_22px_60px_-28px_rgba(59,130,246,0.35)] backdrop-blur dark:border-border/70 dark:bg-card/88 dark:shadow-[0_24px_70px_-32px_rgba(2,6,23,0.82)]"
                    >
                        <slot />
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
