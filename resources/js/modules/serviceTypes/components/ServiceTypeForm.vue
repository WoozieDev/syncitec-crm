<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Tag } from 'lucide-vue-next';
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

const props = defineProps<{
    form: {
        name: string;
        errors: Record<string, string | undefined>;
        processing: boolean;
    };
    cancelHref: string;
    submitLabel: string;
    processingLabel: string;
    description: string;
}>();

const emit = defineEmits<{
    (e: 'submit'): void;
}>();
</script>

<template>
    <Card class="max-w-3xl">
        <CardHeader>
            <CardTitle>Datos del tipo de servicio</CardTitle>
            <CardDescription>
                {{ description }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form class="space-y-6" @submit.prevent="emit('submit')">
                <div class="grid gap-2">
                    <Label
                        for="name"
                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                    >
                        Nombre
                    </Label>
                    <div class="relative">
                        <Tag
                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            id="name"
                            v-model="props.form.name"
                            type="text"
                            placeholder="Ej. Hosting web"
                            class="pl-10"
                        />
                    </div>
                    <InputError :message="props.form.errors.name" />
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t pt-6">
                    <Link :href="cancelHref">
                        <Button type="button" variant="outline" class="cursor-pointer">
                            Cancelar
                        </Button>
                    </Link>
                    <Button type="submit" :disabled="props.form.processing" class="cursor-pointer">
                        {{ props.form.processing ? processingLabel : submitLabel }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
