<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { SidebarMenu, SidebarMenuItem } from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarMenu class="space-y-1 px-3">
        <SidebarMenuItem v-for="item in items" :key="item.title">
            <Link
                :href="item.href"
                class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200"
                :class="[
                    isCurrentOrParentUrl(item.href)
                        ? 'bg-primary/12 text-primary shadow-sm dark:bg-primary/18 dark:text-blue-200'
                        : 'text-sidebar-foreground/65 hover:bg-sidebar-accent/70 hover:text-sidebar-foreground',
                ]"
            >
                <component
                    :is="item.icon"
                    class="size-4 shrink-0 transition"
                    :class="[
                        isCurrentOrParentUrl(item.href)
                            ? 'text-primary dark:text-blue-200'
                            : 'text-sidebar-foreground/55 group-hover:text-sidebar-foreground',
                    ]"
                />

                <span class="truncate">
                    {{ item.title }}
                </span>
            </Link>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
