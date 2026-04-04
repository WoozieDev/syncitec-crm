<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    CalendarDays,
    CheckCircle2,
    KeyRound,
    Mail,
    Pencil,
    ShieldCheck,
    UserRound,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { UserShowProps } from '@/modules/users/types';
import { edit, index } from '@/routes/users';
import type { User as AuthUser } from '@/types/auth';

const props = defineProps<UserShowProps>();
const page = usePage();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: index(),
            },
            {
                title: 'Detalle del usuario',
                href: '#',
            },
        ],
    },
});

const currentUser = computed(() => page.props.auth.user as AuthUser);
const isCurrentUser = computed(() => Number(currentUser.value.id) === props.user.id);

const parseDate = (value?: string | null) => {
    if (!value) {
        return null;
    }

    const parsedDate = new Date(value.replace(' ', 'T'));

    return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
};

const formatDateTime = (value?: string | null): string => {
    const parsedDate = parseDate(value);

    if (!parsedDate) {
        return '-';
    }

    return parsedDate.toLocaleString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`Usuario - ${user.name}`" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1
                            class="text-3xl font-black tracking-tight text-foreground sm:text-4xl xl:text-[3.4rem] xl:leading-none"
                        >
                            {{ user.name }}
                        </h1>

                        <span
                            v-if="isCurrentUser"
                            class="inline-flex items-center rounded-full border border-primary/15 bg-primary/10 px-3 py-1 text-xs font-semibold text-primary uppercase"
                        >
                            Tu cuenta
                        </span>
                    </div>

                    <p class="text-base font-medium text-muted-foreground sm:text-xl">
                        {{ user.email }}
                    </p>
                </div>

                <Link :href="edit(user.id)">
                    <Button
                        variant="outline"
                        class="w-full cursor-pointer sm:w-auto"
                    >
                        <Pencil class="size-4" />
                        <span>Editar usuario</span>
                    </Button>
                </Link>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.05fr)_380px]">
            <Card class="border-border/60">
                <CardHeader>
                    <CardTitle>Informacion del usuario</CardTitle>
                    <CardDescription>
                        Datos base registrados para esta cuenta interna.
                    </CardDescription>
                </CardHeader>

                <CardContent class="grid gap-6 md:grid-cols-2">
                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Nombre
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <UserRound class="size-4 text-muted-foreground" />
                            <p class="font-medium">{{ user.name }}</p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Correo
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <Mail class="size-4 text-muted-foreground" />
                            <p class="font-medium">{{ user.email }}</p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Fecha de registro
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <CalendarDays class="size-4 text-muted-foreground" />
                            <p class="font-medium">
                                {{ formatDateTime(user.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-muted-foreground uppercase"
                        >
                            Ultima actualizacion
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <CalendarDays class="size-4 text-muted-foreground" />
                            <p class="font-medium">
                                {{ formatDateTime(user.updated_at) }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <section
                class="relative overflow-hidden rounded-3xl border border-blue-400/20 bg-linear-to-br from-blue-600 via-blue-700 to-slate-950 p-6 text-white shadow-xl shadow-blue-950/20"
            >
                <div
                    class="absolute -top-20 -right-12 h-44 w-44 rounded-full bg-white/10 blur-3xl"
                />
                <div
                    class="absolute -bottom-20 -left-12 h-40 w-40 rounded-full bg-cyan-300/10 blur-3xl"
                />

                <div class="relative space-y-6">
                    <div>
                        <p
                            class="text-xs font-semibold tracking-[0.22em] text-white/65 uppercase"
                        >
                            Estado de seguridad
                        </p>
                        <p class="mt-2 text-sm text-white/70">
                            Resumen rapido de verificacion y proteccion de la
                            cuenta.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <div class="flex items-center gap-3">
                                <CheckCircle2
                                    v-if="user.email_verified_at"
                                    class="size-5 text-emerald-300"
                                />
                                <XCircle
                                    v-else
                                    class="size-5 text-amber-300"
                                />

                                <div>
                                    <p class="text-sm font-semibold">
                                        {{
                                            user.email_verified_at
                                                ? 'Correo verificado'
                                                : 'Correo pendiente'
                                        }}
                                    </p>
                                    <p class="text-xs text-white/65">
                                        {{
                                            user.email_verified_at
                                                ? formatDateTime(
                                                      user.email_verified_at,
                                                  )
                                                : 'Aun no se ha confirmado el correo.'
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl border border-white/10 bg-black/15 p-4"
                        >
                            <div class="flex items-center gap-3">
                                <ShieldCheck
                                    class="size-5"
                                    :class="
                                        user.two_factor_enabled
                                            ? 'text-cyan-300'
                                            : 'text-white/45'
                                    "
                                />

                                <div>
                                    <p class="text-sm font-semibold">
                                        {{
                                            user.two_factor_enabled
                                                ? 'Doble factor activo'
                                                : 'Doble factor inactivo'
                                        }}
                                    </p>
                                    <p class="text-xs text-white/65">
                                        {{
                                            user.two_factor_confirmed_at
                                                ? formatDateTime(
                                                      user.two_factor_confirmed_at,
                                                  )
                                                : 'Sin configuracion confirmada.'
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-white/15 pt-4">
                        <div class="flex items-center gap-3">
                            <KeyRound class="size-4 text-white/70" />
                            <p class="text-sm text-white/75">
                                La contraseña no se muestra por seguridad.
                                Cualquier cambio se gestiona desde la edicion
                                del usuario.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
