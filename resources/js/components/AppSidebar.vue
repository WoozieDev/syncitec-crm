<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import type { InertiaLinkProps } from '@inertiajs/vue3';
import {
    BriefcaseBusiness,
    CheckSquare,
    CircleHelp,
    FolderKanban,
    HandCoins,
    LayoutGrid,
    LogOut,
    Settings2,
    ShieldCheck,
    SunMoon,
    UserCog,
    Users,
    Wrench,
} from 'lucide-vue-next';
import type { LucideIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { dashboard, logout } from '@/routes';
import { edit as appearanceEdit } from '@/routes/appearance';
import { index as clientsIndex } from '@/routes/clients';
import { index as personalTasksIndex } from '@/routes/personal-tasks';
import { edit as profileEdit } from '@/routes/profile';
import { index as projectPaymentsIndex } from '@/routes/project-payments';
import { index as projectsIndex } from '@/routes/projects';
import { index as servicePaymentsIndex } from '@/routes/service-payments';
import { index as servicesIndex } from '@/routes/services';
import { index as usersIndex } from '@/routes/users';

type SidebarNavItem = {
    title: string;
    href?: NonNullable<InertiaLinkProps['href']>;
    icon: LucideIcon;
    disabled?: boolean;
    exact?: boolean;
    badge?: string;
};

type SidebarNavGroup = {
    title: string;
    items: SidebarNavItem[];
};

const navigationGroups: SidebarNavGroup[] = [
    {
        title: 'General',
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
                exact: true,
            },
            {
                title: 'Tareas personales',
                href: personalTasksIndex(),
                icon: CheckSquare,
            },
        ],
    },
    {
        title: 'CRM',
        items: [
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
        ],
    },
    {
        title: 'Pagos',
        items: [
            {
                title: 'Pagos de proyectos',
                href: projectPaymentsIndex(),
                icon: HandCoins,
            },
            {
                title: 'Pagos de servicios',
                href: servicePaymentsIndex(),
                icon: HandCoins,
            },
        ],
    },
    {
        title: 'Operacion',
        items: [
            {
                title: 'Servicios',
                href: servicesIndex(),
                icon: Wrench,
            },
            {
                title: 'Proveedores',
                icon: BriefcaseBusiness,
                disabled: true,
                badge: 'Prox.',
            },
        ],
    },
    {
        title: 'Seguridad',
        items: [
            {
                title: 'Usuarios',
                href: usersIndex(),
                icon: UserCog,
            },
            {
                title: 'Roles',
                icon: ShieldCheck,
                disabled: true,
                badge: 'Prox.',
            },
        ],
    },
    {
        title: 'Configuracion',
        items: [
            {
                title: 'Mi Perfil',
                href: profileEdit(),
                icon: Settings2,
                exact: true,
            },
            {
                title: 'Apariencia y tema',
                href: appearanceEdit(),
                icon: SunMoon,
                exact: true,
            },
        ],
    },
];

const { isCurrentOrParentUrl, isCurrentUrl } = useCurrentUrl();

const hasEnabledItems = computed(() =>
    navigationGroups.some((group) =>
        group.items.some((item) => !item.disabled && item.href),
    ),
);

const isItemActive = (item: SidebarNavItem) => {
    if (!item.href || item.disabled) {
        return false;
    }

    return item.exact
        ? isCurrentUrl(item.href)
        : isCurrentOrParentUrl(item.href);
};

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <Sidebar
        collapsible="offcanvas"
        variant="sidebar"
        class="border-r border-sidebar-border/40 md:*:data-[sidebar=sidebar]:bg-sidebar/95"
    >
        <SidebarHeader class="px-6 pt-7 pb-5">
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
            <div v-if="hasEnabledItems" class="space-y-1 px-2">
                <SidebarGroup
                    v-for="group in navigationGroups"
                    :key="group.title"
                    class="px-2 py-1"
                >
                    <SidebarGroupLabel
                        class="px-3 text-[11px] font-semibold tracking-[0.16em] uppercase"
                    >
                        {{ group.title }}
                    </SidebarGroupLabel>

                    <SidebarGroupContent>
                        <SidebarMenu class="space-y-1">
                            <SidebarMenuItem
                                v-for="item in group.items"
                                :key="`${group.title}-${item.title}`"
                            >
                                <SidebarMenuButton
                                    v-if="item.href && !item.disabled"
                                    as-child
                                    size="lg"
                                    :is-active="isItemActive(item)"
                                    :tooltip="item.title"
                                    class="group rounded-xl px-3 text-sm font-medium transition-all duration-200 data-[active=true]:bg-primary/12 data-[active=true]:text-primary data-[active=true]:shadow-sm hover:bg-sidebar-accent/70 dark:data-[active=true]:bg-primary/18 dark:data-[active=true]:text-blue-200"
                                >
                                    <Link :href="item.href">
                                        <component
                                            :is="item.icon"
                                            class="size-4 shrink-0 transition group-data-[active=true]:text-primary dark:group-data-[active=true]:text-blue-200"
                                        />
                                        <span class="truncate">
                                            {{ item.title }}
                                        </span>
                                    </Link>
                                </SidebarMenuButton>

                                <SidebarMenuButton
                                    v-else
                                    size="lg"
                                    disabled
                                    :tooltip="`${item.title} proximamente`"
                                    class="rounded-xl px-3 text-sm font-medium text-sidebar-foreground/45 opacity-100"
                                >
                                    <component
                                        :is="item.icon"
                                        class="size-4 shrink-0 text-sidebar-foreground/35"
                                    />
                                    <span class="truncate">
                                        {{ item.title }}
                                    </span>
                                    <span
                                        v-if="item.badge"
                                        class="ml-auto rounded-full border border-sidebar-border/60 px-2 py-0.5 text-[10px] font-semibold tracking-wide text-sidebar-foreground/55 uppercase"
                                    >
                                        {{ item.badge }}
                                    </span>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </div>
        </SidebarContent>

        <SidebarFooter class="px-5 pt-4 pb-6">
            <div class="space-y-1 border-t border-sidebar-border/35 pt-4">
                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-sidebar-foreground/60 transition hover:bg-sidebar-accent/60 hover:text-sidebar-foreground"
                >
                    <CircleHelp class="size-4" />
                    <span>Soporte</span>
                </button>

                <Link
                    :href="logout()"
                    as="button"
                    type="button"
                    class="flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-sidebar-foreground/60 transition hover:bg-sidebar-accent/60 hover:text-destructive"
                    @click="handleLogout"
                >
                    <LogOut class="size-4" />
                    <span>Cerrar sesion</span>
                </Link>
            </div>
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
