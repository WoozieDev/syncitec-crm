<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { ProjectShowProps } from '@/modules/projects';
import { edit, index } from '@/routes/projects';

defineProps<ProjectShowProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Projects',
                href: index(),
            },
            {
                title: 'Project Details',
                href: '#',
            },
        ],
    },
});

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number): string => currencyFormatter.format(value);
</script>

<template>
    <Head :title="`Project - ${project.name}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col gap-6">
            <div class="flex items-start justify-between gap-3">
                <Heading
                    variant="small"
                    :title="project.name"
                    :description="`Project code: ${project.project_code}`"
                />
                <Link :href="edit(project.id)">
                    <Button variant="outline">Edit Project</Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Project Summary</CardTitle>
                    <CardDescription>
                        Main details for quick project review.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Customer</p>
                        <p class="mt-1 font-medium">{{ project.client.display_name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Status</p>
                        <p class="mt-1 font-medium">{{ project.status_label }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Total Amount</p>
                        <p class="mt-1 font-medium">{{ formatCurrency(project.total_amount) }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Balance Due</p>
                        <p class="mt-1 font-medium">{{ formatCurrency(project.balance_due) }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Start Date</p>
                        <p class="mt-1 font-medium">{{ project.start_date ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Due Date</p>
                        <p class="mt-1 font-medium">{{ project.due_date ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Description</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ project.description }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-muted-foreground uppercase">Notes</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ project.notes || 'No notes' }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
