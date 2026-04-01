<script setup lang="ts">
import type { LucideIcon } from 'lucide-vue-next';
import {
    BriefcaseBusiness,
    ShieldCheck,
    Sparkles,
    Workflow,
} from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

type Highlight = {
    title: string;
    description: string;
    icon: LucideIcon;
};

const highlights: Highlight[] = [
    {
        title: 'Unified pipeline',
        description:
            'Track leads, follow-ups, and account activity from one workspace.',
        icon: Workflow,
    },
    {
        title: 'Secure access',
        description:
            'Theme-aware UI and protected sessions ready for real auth flows.',
        icon: ShieldCheck,
    },
    {
        title: 'Clean foundation',
        description:
            'Component-based layout prepared to plug real data and providers later.',
        icon: Sparkles,
    },
];
</script>

<template>
    <section class="flex flex-col justify-between gap-8 lg:pr-8">
        <div class="space-y-6">
            <div
                class="inline-flex items-center gap-3 rounded-full border border-border/70 bg-background/80 px-4 py-2 shadow-sm backdrop-blur"
            >
                <span
                    class="flex size-10 items-center justify-center rounded-full bg-primary/12 text-primary"
                >
                    <AppLogoIcon className="size-5" />
                </span>

                <div class="text-left">
                    <p
                        class="text-xs font-semibold tracking-[0.24em] text-muted-foreground uppercase"
                    >
                        Syncitec CRM
                    </p>
                    <p class="text-sm text-muted-foreground">
                        Auth redesign mockup
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <span
                    class="inline-flex items-center rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-xs font-semibold tracking-[0.24em] text-primary uppercase"
                >
                    Responsive and theme-aware
                </span>

                <div class="space-y-3">
                    <h1
                        class="max-w-2xl text-4xl font-semibold tracking-tight text-foreground sm:text-5xl"
                    >
                        Clean access for the CRM workspace.
                    </h1>
                    <p
                        class="max-w-xl text-base leading-7 text-muted-foreground"
                    >
                        This screen translates the exported design into reusable
                        Vue components, current project theme tokens, and a
                        responsive layout ready for real integrations later.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article
                v-for="item in highlights"
                :key="item.title"
                class="rounded-3xl border border-border/70 bg-card/70 p-5 shadow-sm backdrop-blur"
            >
                <div
                    class="mb-4 inline-flex size-11 items-center justify-center rounded-2xl bg-primary/12 text-primary"
                >
                    <component :is="item.icon" class="size-5" />
                </div>

                <h2 class="text-base font-semibold text-foreground">
                    {{ item.title }}
                </h2>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    {{ item.description }}
                </p>
            </article>
        </div>

        <div
            class="rounded-4xl border border-border/70 bg-card/80 p-6 shadow-sm backdrop-blur"
        >
            <div class="flex items-start gap-4">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-accent/12 text-accent"
                >
                    <BriefcaseBusiness class="size-5" />
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-semibold text-foreground">
                        Mock content, production-ready structure
                    </p>
                    <p class="text-sm leading-6 text-muted-foreground">
                        The copy and support actions are placeholder content for
                        the layout only. The primary email/password submission
                        stays wired to the current Fortify login flow.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
