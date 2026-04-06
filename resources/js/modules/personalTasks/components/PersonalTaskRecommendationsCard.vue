<script setup lang="ts">
import { CalendarRange, CircleCheckBig, Flag, GripVertical } from 'lucide-vue-next';
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
                El modulo ahora funciona como planner personal ligero tipo Asana.
            </CardDescription>
        </CardHeader>

        <CardContent class="space-y-4 text-sm text-muted-foreground">
            <div class="flex items-start gap-3">
                <CalendarRange class="mt-0.5 size-4 text-primary" />
                <p>
                    Usa la fecha objetivo para que la tarea caiga en Hoy, Esta
                    semana o Proxima semana.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <Flag class="mt-0.5 size-4 text-primary" />
                <p>
                    La prioridad usa semaforo visual: rojo para alta, amarillo
                    para media y verde para baja.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <CircleCheckBig class="mt-0.5 size-4 text-primary" />
                <p>
                    Completada registra cierre automatico para separar trabajo
                    activo de historial reciente.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <GripVertical class="mt-0.5 size-4 text-primary" />
                <p>
                    El orden ya no se captura manualmente; queda interno para el
                    tablero y movimiento entre paneles.
                </p>
            </div>
        </CardContent>
    </Card>
</template>
