<script setup lang="ts">
import { CheckCircle2, Lightbulb, ListOrdered } from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
</script>

<template>
    <Card class="border-border/60">
        <CardHeader>
            <CardTitle>Guia rapida</CardTitle>
            <CardDescription>
                Mantiene la tarea consistente con el flujo real del proyecto.
            </CardDescription>
        </CardHeader>

        <CardContent class="space-y-4 text-sm text-muted-foreground">
            <div class="flex items-start gap-3">
                <Lightbulb class="mt-0.5 size-4 text-primary" />
                <p>
                    Usa el modulo solo cuando la tarea pertenezca a una fase real
                    del proyecto.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <CheckCircle2 class="mt-0.5 size-4 text-primary" />
                <p>
                    El estado controla el seguimiento operativo y luego servira
                    para la vista Kanban.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <ListOrdered class="mt-0.5 size-4 text-primary" />
                <p>
                    El orden permite mantener secuencia sin cambiar la estructura
                    de datos actual.
                </p>
            </div>
        </CardContent>
    </Card>
</template>
