<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CircleUserRound } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import ProfileAccountFormCard from '@/modules/profile/components/ProfileAccountFormCard.vue';
import ProfileRecommendationsCard from '@/modules/profile/components/ProfileRecommendationsCard.vue';
import type { ProfilePageProps } from '@/modules/profile/types';
import { edit } from '@/routes/profile';

defineProps<ProfilePageProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Mi perfil',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Mi perfil" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto bg-linear-to-b from-muted/20 via-transparent to-transparent p-4 sm:p-6"
    >
        <section
            class="rounded-3xl border border-border/60 bg-card/70 p-5 shadow-sm backdrop-blur-sm sm:p-6"
        >
            <div class="space-y-4">
                <Heading
                    variant="small"
                    title="Mi perfil"
                    description="Gestiona tu informacion personal y confirma los cambios en tu cuenta principal."
                />

                <div class="flex flex-wrap items-center gap-3">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/10 px-3 py-1.5 text-sm text-primary"
                    >
                        <CircleUserRound class="size-4" />
                        <span class="font-semibold">Cuenta personal</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
            <ProfileAccountFormCard
                :user="user"
                :must-verify-email="mustVerifyEmail"
                :status="status"
            />
            <ProfileRecommendationsCard />
        </div>
    </div>
</template>
