<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Bell, Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useAppearance } from '@/composables/useAppearance';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem } from '@/types';

type Props = {
    showSidebarTrigger?: boolean;
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    showSidebarTrigger: false,
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const { appearance, resolvedAppearance, updateAppearance } = useAppearance();
const hasMounted = ref(false);

onMounted(() => {
    hasMounted.value = true;
});

const themeIcon = computed(() =>
    !hasMounted.value && appearance.value === 'system'
        ? Monitor
        : resolvedAppearance.value === 'dark'
          ? Moon
          : Sun,
);

const themeLabel = computed(() =>
    !hasMounted.value && appearance.value === 'system'
        ? 'Cambiar tema'
        : resolvedAppearance.value === 'dark'
          ? 'Tema oscuro'
          : 'Tema claro',
);

const toggleAppearance = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <header
        class="sticky top-0 z-30 border-b border-border/60 bg-background/85 backdrop-blur-xl"
    >
        <div class="flex h-16 items-center gap-4 px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <SidebarTrigger
                    v-if="props.showSidebarTrigger"
                    class="md:hidden"
                />
            </div>

            <div
                v-if="props.breadcrumbs.length > 0"
                class="hidden min-w-0 flex-1 md:block"
            >
                <Breadcrumbs :breadcrumbs="props.breadcrumbs" />
            </div>

            <div class="ml-auto flex items-center gap-2">
                <div class="flex items-center gap-1">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="rounded-full text-muted-foreground"
                        disabled
                        aria-label="Notificaciones proximamente"
                    >
                        <Bell class="size-4" />
                    </Button>

                    <Button
                        variant="ghost"
                        size="icon"
                        class="rounded-full text-muted-foreground"
                        :aria-label="themeLabel"
                        :title="themeLabel"
                        @click="toggleAppearance"
                    >
                        <component :is="themeIcon" class="size-4" />
                    </Button>
                </div>

                <div class="mx-2 h-7 w-px bg-border/70" aria-hidden="true" />

                <div class="ml-1 flex items-center gap-1">

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="ghost"
                                class="relative h-11 rounded-full px-2 py-1"
                            >
                                <div class="flex items-center gap-3">
                                    <Avatar
                                        class="h-8 w-8 ring-2 ring-primary/15"
                                    >
                                        <AvatarImage
                                            v-if="auth.user.avatar"
                                            :src="auth.user.avatar"
                                            :alt="auth.user.name"
                                        />
                                        <AvatarFallback
                                            class="bg-primary/12 text-primary"
                                        >
                                            {{ getInitials(auth.user?.name) }}
                                        </AvatarFallback>
                                    </Avatar>

                                    <span
                                        class="hidden max-w-40 truncate text-sm font-semibold text-foreground sm:block"
                                    >
                                        {{ auth.user.name }}
                                    </span>
                                </div>
                            </Button>
                        </DropdownMenuTrigger>

                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>
    </header>
</template>
