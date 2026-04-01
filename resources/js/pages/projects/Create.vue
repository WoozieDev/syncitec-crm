<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { ProjectCreateProps, ProjectFormData } from '@/modules/projects';
import { index, store } from '@/routes/projects';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<ProjectCreateProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Projects',
                href: index(),
            },
            {
                title: 'New Project',
                href: '#',
            },
        ],
    },
});

const form = useForm<ProjectFormData>({
    client_id: '',
    name: '',
    description: '',
    status: props.status_options[0]?.value ?? 'draft',
    price: '',
    start_date: '',
    due_date: '',
    notes: '',
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        client_id:
            data.client_id === '' ? '' : Number(data.client_id),
        price: data.price === '' ? '' : Number(data.price),
    }));

    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="New Project" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="New Project"
                description="Create a new project and assign it to a customer."
            />

            <Card>
                <CardHeader>
                    <CardTitle>Project Information</CardTitle>
                    <CardDescription>
                        Fill in the key details to register this project.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <form class="space-y-6" @submit.prevent="submit">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="client_id">Customer</Label>
                                <select
                                    id="client_id"
                                    v-model="form.client_id"
                                    class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                                >
                                    <option :value="''">Select a customer</option>
                                    <option
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="client.id"
                                    >
                                        {{ client.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.client_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="status">Status</Label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                                >
                                    <option
                                        v-for="status in status_options"
                                        :key="status.value"
                                        :value="status.value"
                                    >
                                        {{ status.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.status" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="name">Project Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Website Redesign 2.0"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="description">Description</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Describe project scope, objectives, and deliverables..."
                                />
                                <InputError :message="form.errors.description" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="price">Total Amount</Label>
                                <Input
                                    id="price"
                                    v-model.number="form.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="12500.00"
                                />
                                <InputError :message="form.errors.price" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="start_date">Start Date</Label>
                                <Input id="start_date" v-model="form.start_date" type="date" />
                                <InputError :message="form.errors.start_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="due_date">Due Date</Label>
                                <Input id="due_date" v-model="form.due_date" type="date" />
                                <InputError :message="form.errors.due_date" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="notes">Notes</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    placeholder="Internal notes..."
                                />
                                <InputError :message="form.errors.notes" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Create Project' }}
                            </Button>
                            <Link :href="index()">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
