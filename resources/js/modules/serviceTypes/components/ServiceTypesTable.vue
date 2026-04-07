<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Pencil, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import type { PaginationLink, ServiceType } from '@/modules/serviceTypes/types';
import { edit } from '@/routes/serviceTypes';

const props = defineProps<{
    items: ServiceType[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
}>();

const emit = defineEmits<{
    (e: 'delete', serviceType: ServiceType): void;
}>();

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const previousLink = computed(() => props.links[0] ?? null);
const nextLink = computed(() => props.links[props.links.length - 1] ?? null);
</script>

<template>
    <section
        class="rounded-4xl border border-border/60 bg-muted/50 p-4 shadow-sm sm:p-6 dark:bg-slate-900/40"
    >
        <div class="hidden px-6 pb-3 lg:grid lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_auto]">
            <p class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase">
                Tipo de servicio
            </p>
            <p class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase">
                Servicios asociados
            </p>
            <p class="text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase">
                ID
            </p>
            <p class="text-right text-[11px] font-bold tracking-[0.18em] text-muted-foreground uppercase">
                Acciones
            </p>
        </div>

        <div class="space-y-4">
            <Card
                v-for="serviceType in items"
                :key="serviceType.id"
                class="gap-0 rounded-3xl border-border/40 py-0 transition-colors hover:bg-accent/30"
            >
                <div class="grid grid-cols-1 gap-4 p-4 md:p-5 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-center lg:gap-3">
                    <div>
                        <p class="text-sm font-bold text-foreground">
                            {{ serviceType.name }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden">
                            Servicios asociados
                        </p>
                        <p class="text-sm text-foreground">
                            {{ serviceType.services_count }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-[10px] font-bold tracking-[0.18em] text-muted-foreground uppercase lg:hidden">
                            ID
                        </p>
                        <p class="text-sm text-foreground">#{{ serviceType.id }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <Link
                            :href="edit(serviceType.id)"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            title="Editar"
                        >
                            <Pencil class="size-4" />
                        </Link>

                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-lg text-destructive transition-colors hover:bg-destructive/10"
                            title="Eliminar"
                            @click="emit('delete', serviceType)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>
            </Card>

            <Card v-if="items.length === 0" class="rounded-3xl border-dashed py-10">
                <p class="text-center text-sm text-muted-foreground">
                    No se encontraron tipos de servicio.
                </p>
            </Card>
        </div>

        <div
            class="mt-6 flex flex-col gap-4 text-xs font-medium text-muted-foreground md:mt-8 md:flex-row md:items-center md:justify-between"
        >
            <p>
                Mostrando
                <span class="font-bold text-foreground">{{ from }} - {{ to }}</span>
                de {{ total.toLocaleString('es-ES') }} tipos
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

                <Link
                    v-for="link in numericLinks"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    class="inline-flex size-8 items-center justify-center rounded-lg text-sm transition-colors"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : link.url
                              ? 'text-muted-foreground hover:bg-background hover:text-foreground'
                              : 'pointer-events-none text-muted-foreground/40'
                    "
                >
                    {{ link.label }}
                </Link>

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
