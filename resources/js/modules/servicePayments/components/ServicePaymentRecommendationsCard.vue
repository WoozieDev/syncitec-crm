<script setup lang="ts">
import { Sparkles } from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const tips = [
    'Registra el metodo exactamente como figura en el comprobante.',
    'Usa notas solo para contexto real del pago o referencia bancaria.',
    'Confirma el servicio antes de guardar para evitar pagos mal asociados.',
];
</script>

<template>
    <Card
        class="h-fit overflow-hidden rounded-3xl border-blue-500/20 bg-linear-to-br from-blue-600 via-blue-700 to-slate-950 text-white"
    >
        <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base text-white">
                <Sparkles class="size-4" />
                Pago inteligente
            </CardTitle>
            <CardDescription class="text-white/70">
                Recomendaciones para mantener conciliacion y trazabilidad.
            </CardDescription>
        </CardHeader>

        <CardContent class="space-y-3 text-sm text-white/80">
            <p
                v-for="(tip, index) in tips"
                :key="tip"
                class="rounded-2xl border border-white/10 bg-black/15 px-3 py-3"
            >
                <span class="mr-2 font-semibold text-cyan-200">0{{ index + 1 }}</span>
                {{ tip }}
            </p>
        </CardContent>
    </Card>
</template>
