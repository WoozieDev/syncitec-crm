<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Bell } from 'lucide-vue-next';
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';

type Props = {
    showSidebarTrigger?: boolean;
};

withDefaults(defineProps<Props>(), {
    showSidebarTrigger: false,
});

const page = usePage();
const auth = computed(() => page.props.auth);
</script>

<template>
    <header
        class="sticky top-0 z-30 border-b border-border/60 bg-background/85 backdrop-blur-xl"
    >
        <div class="flex h-16 items-center gap-4 px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <SidebarTrigger v-if="showSidebarTrigger" class="md:hidden" />
            </div>

            <div class="ml-auto flex items-center gap-2">
                <div
                    class="ml-1 flex items-center gap-1 border-l border-border/60 pl-3"
                >
                    <Button
                        variant="ghost"
                        size="icon"
                        class="rounded-full text-muted-foreground"
                    >
                        <Bell class="size-4" />
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="ghost"
                                class="relative ml-1 h-10 w-10 rounded-full p-0"
                            >
                                <Avatar class="h-8 w-8 ring-2 ring-primary/15">
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
