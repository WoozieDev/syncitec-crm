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
                Mantiene tus tareas personales ordenadas y reutilizables.
            </CardDescription>
        </CardHeader>

        <CardContent class="space-y-4 text-sm text-muted-foreground">
            <div class="flex items-start gap-3">
                <Lightbulb class="mt-0.5 size-4 text-primary" />
                <p>
                    Usa un titulo corto y accionable para identificar rapido la
                    tarea.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <CheckCircle2 class="mt-0.5 size-4 text-primary" />
                <p>
                    El estado y la prioridad mantienen una base limpia para la
                    futura vista Kanban.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <ListOrdered class="mt-0.5 size-4 text-primary" />
                <p>
                    El orden te permite decidir secuencia sin cambiar la
                    estructura real de la tabla.
                </p>
            </div>
        </CardContent>
    </Card>
</template>
