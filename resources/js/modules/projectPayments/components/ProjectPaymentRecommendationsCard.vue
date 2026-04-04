<script setup lang="ts">
import { Lightbulb } from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const tips = [
    'Registra el metodo tal como figura en el comprobante real.',
    'Mantener notas claras facilita conciliacion y auditoria interna.',
    'Verifica el saldo del proyecto antes de guardar el pago.',
];
</script>

<template>
    <Card class="h-fit border-primary/20 bg-primary/5">
        <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base">
                <Lightbulb class="size-4 text-primary" />
                Recomendaciones
            </CardTitle>
            <CardDescription>
                Buenas practicas para registrar pagos de proyecto.
            </CardDescription>
        </CardHeader>

        <CardContent class="space-y-3 text-sm text-muted-foreground">
            <p
                v-for="(tip, index) in tips"
                :key="tip"
                class="rounded-md border bg-background px-3 py-2"
            >
                <span class="mr-2 font-semibold text-primary">0{{ index + 1 }}</span>
                {{ tip }}
            </p>
        </CardContent>
    </Card>
</template>

