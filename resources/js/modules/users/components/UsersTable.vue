<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Eye,
    Pencil,
    ShieldCheck,
    Trash2,
    UserRound,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import type { PaginationLink, UserRecord } from '@/modules/users/types';
import { edit, show } from '@/routes/users';
import type { User as AuthUser } from '@/types/auth';

const props = defineProps<{
    items: UserRecord[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
}>();

const emit = defineEmits<{
    (e: 'delete', user: UserRecord): void;
}>();

const page = usePage();

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const previousLink = computed(() => props.links[0] ?? null);
const nextLink = computed(() => props.links[props.links.length - 1] ?? null);
const currentUser = computed(() => page.props.auth.user as AuthUser);

const userInitials = (name: string) =>
    name
        .trim()
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('');

const isCurrentUser = (user: UserRecord) => Number(currentUser.value.id) === user.id;

const formatCreatedAt = (value?: string | null) => {
    if (!value) {
        return {
            date: '-',
            time: '-',
        };
    }

    const parsedDate = new Date(value.replace(' ', 'T'));

    if (Number.isNaN(parsedDate.getTime())) {
        return {
            date: '-',
            time: '-',
        };
    }

    return {
        date: parsedDate.toLocaleDateString('es-ES', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        }),
        time: parsedDate.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
        }),
    };
};
</script>

<template>
    <section
        class="rounded-4xl border border-border/60 bg-muted/50 p-4 shadow-sm sm:p-6 dark:bg-slate-900/40"
    >
        <div
            class="hidden px-6 pb-3 lg:grid lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1.2fr)_minmax(0,0.9fr)_minmax(0,0.9fr)_minmax(0,0.8fr)_auto]"
        >
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Usuario
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Correo
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Verificacion
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Doble factor
            </p>
            <p
                class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Fecha de alta
            </p>
            <p
                class="text-right text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase"
            >
                Acciones
            </p>
        </div>

        <div class="space-y-4">
            <Card
                v-for="user in items"
                :key="user.id"
                class="gap-0 rounded-3xl border-border/40 py-0 transition-colors hover:bg-accent/30"
            >
                <div
                    class="grid grid-cols-1 gap-4 p-4 md:p-5 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1.2fr)_minmax(0,0.9fr)_minmax(0,0.9fr)_minmax(0,0.8fr)_auto] lg:items-center lg:gap-3"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex size-10 items-center justify-center rounded-xl bg-primary/10 text-xs font-bold text-primary"
                        >
                            {{ userInitials(user.name) }}
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <Link
                                    :href="show(user.id)"
                                    class="text-sm font-bold text-foreground transition-colors hover:text-primary"
                                >
                                    {{ user.name }}
                                </Link>

                                <span
                                    v-if="isCurrentUser(user)"
                                    class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary"
                                >
                                    Tu cuenta
                                </span>
                            </div>

                            <p class="text-[11px] text-muted-foreground">
                                ID: #{{ user.id }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Correo
                        </p>
                        <p class="text-sm font-medium text-foreground">
                            {{ user.email }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Verificacion
                        </p>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.14em] uppercase"
                            :class="
                                user.email_verified_at
                                    ? 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-200'
                                    : 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/15 dark:text-amber-200'
                            "
                        >
                            {{ user.email_verified_at ? 'Verificado' : 'Pendiente' }}
                        </span>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Doble factor
                        </p>
                        <span
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-bold tracking-[0.14em] uppercase"
                            :class="
                                user.two_factor_enabled
                                    ? 'bg-blue-500/15 text-blue-700 dark:bg-blue-400/15 dark:text-blue-200'
                                    : 'bg-slate-500/15 text-slate-700 dark:bg-slate-400/15 dark:text-slate-200'
                            "
                        >
                            <ShieldCheck class="size-3.5" />
                            {{ user.two_factor_enabled ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden"
                        >
                            Fecha de alta
                        </p>
                        <p class="text-sm text-foreground">
                            {{ formatCreatedAt(user.created_at).date }}
                        </p>
                        <p class="text-[11px] text-muted-foreground">
                            {{ formatCreatedAt(user.created_at).time }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <Link
                            :href="show(user.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Ver detalle"
                        >
                            <Eye class="size-4" />
                        </Link>

                        <Link
                            :href="edit(user.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Editar"
                        >
                            <Pencil class="size-4" />
                        </Link>

                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-lg transition-colors"
                            :class="
                                isCurrentUser(user)
                                    ? 'cursor-not-allowed text-muted-foreground/35'
                                    : 'text-destructive hover:bg-destructive/10'
                            "
                            :disabled="isCurrentUser(user)"
                            title="Eliminar"
                            @click="emit('delete', user)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>
            </Card>

            <Card
                v-if="items.length === 0"
                class="rounded-3xl border-dashed py-10"
            >
                <div class="flex flex-col items-center gap-2 text-center">
                    <UserRound class="size-5 text-muted-foreground" />
                    <p class="text-sm text-muted-foreground">
                        No se encontraron usuarios.
                    </p>
                </div>
            </Card>
        </div>

        <div
            class="mt-6 flex flex-col gap-4 text-xs font-medium text-muted-foreground md:mt-8 md:flex-row md:items-center md:justify-between"
        >
            <p>
                Mostrando
                <span class="font-bold text-foreground"
                    >{{ from }} - {{ to }}</span
                >
                de {{ total.toLocaleString('es-ES') }} usuarios
            </p>

            <div class="flex items-center gap-1">
                <Link
                    v-if="previousLink?.url"
                    :href="previousLink.url"
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-background hover:text-foreground"
                >
                    <ChevronLeft class="size-4" />
                </Link>
                <span
                    v-else
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground/40"
                >
                    <ChevronLeft class="size-4" />
                </span>

                <template v-for="link in numericLinks" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-xs font-bold transition-colors"
                        :class="
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:bg-background hover:text-foreground'
                        "
                    >
                        {{ link.label }}
                    </Link>
                </template>

                <Link
                    v-if="nextLink?.url"
                    :href="nextLink.url"
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-background hover:text-foreground"
                >
                    <ChevronRight class="size-4" />
                </Link>
                <span
                    v-else
                    class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground/40"
                >
                    <ChevronRight class="size-4" />
                </span>
            </div>
        </div>
    </section>
</template>
