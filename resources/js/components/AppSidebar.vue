<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    CircleHelp,
    FolderKanban,
    HandCoins,
    KanbanSquare,
    LayoutGrid,
    LogOut,
    Users,
    Wrench,
} from 'lucide-vue-next';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as clientsIndex } from '@/routes/clients';
import { index as projectsIndex } from '@/routes/projects';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clientes',
        href: clientsIndex(),
        icon: Users,
    },
    {
        title: 'Proyectos',
        href: projectsIndex(),
        icon: FolderKanban,
    },
    {
        title: 'Pagos',
        href: '#',
        icon: HandCoins,
    },
    {
        title: 'Servicios',
        href: '#',
        icon: Wrench,
    },
    {
        title: 'Kanban',
        href: '#',
        icon: KanbanSquare,
    },
];
</script>

<template>
    <Sidebar
        collapsible="offcanvas"
        variant="sidebar"
        class="border-r-0 md:*:data-[sidebar=sidebar]:bg-[#eef3f8] dark:md:*:data-[sidebar=sidebar]:bg-[#1f2937]"
    >
        <SidebarHeader class="px-6 pt-7 pb-6">
            <Link :href="dashboard()" class="block">
                <div class="space-y-1">
                    <h1
                        class="text-lg font-black tracking-tight text-sidebar-foreground"
                    >
                        Syncitec CRM
                    </h1>
                    <p
                        class="text-[10px] font-bold tracking-[0.24em] text-sidebar-foreground/50 uppercase"
                    >
                        Premium Management
                    </p>
                </div>
            </Link>
        </SidebarHeader>

        <SidebarContent class="pb-4">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="px-5 pt-4 pb-6">
            <div class="space-y-1 border-t border-sidebar-border/35 pt-4">
                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-sidebar-foreground/60 transition hover:bg-sidebar-accent/60 hover:text-sidebar-foreground"
                >
                    <CircleHelp class="size-4" />
                    <span>Support</span>
                </button>

                <button
                    type="button"
                    class="flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-sidebar-foreground/60 transition hover:bg-sidebar-accent/60 hover:text-destructive"
                >
                    <LogOut class="size-4" />
                    <span>Sign Out</span>
                </button>
            </div>
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
