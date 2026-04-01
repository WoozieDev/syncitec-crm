<script setup lang="ts">
import { Rocket, UserPlus } from 'lucide-vue-next';
</script>

<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <article
            class="group relative flex min-h-48 flex-col justify-between overflow-hidden rounded-2xl bg-gradient-to-br from-primary to-blue-500 p-7 text-primary-foreground shadow-lg shadow-primary/20"
        >
            <div class="relative z-10 space-y-4">
                <UserPlus class="size-9 opacity-90" />
                <div>
                    <h4 class="text-3xl font-bold tracking-tight">
                        New Customer
                    </h4>
                    <p class="text-sm text-primary-foreground/75">
                        Onboard a new client to the system
                    </p>
                </div>
            </div>

            <span
                class="absolute -bottom-5 right-0 text-[120px] font-black text-white/10 transition-transform duration-300 group-hover:rotate-6"
            >
                +
            </span>
        </article>

        <article
            class="group relative flex min-h-48 flex-col justify-between overflow-hidden rounded-2xl border border-border/50 bg-card p-7 shadow-sm dark:border-white/6 dark:bg-[#0b1120]"
        >
            <div class="relative z-10 space-y-4">
                <Rocket class="size-9 text-primary" />
                <div>
                    <h4 class="text-3xl font-bold tracking-tight text-foreground">
                        New Project
                    </h4>
                    <p class="text-sm text-muted-foreground">
                        Initialize a new workflow for a client
                    </p>
                </div>
            </div>

            <Rocket
                class="absolute -bottom-6 right-0 size-28 rotate-12 text-primary/8 transition-transform duration-300 group-hover:rotate-0"
            />
        </article>
    </div>
</template>
