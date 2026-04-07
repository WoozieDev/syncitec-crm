<script setup lang="ts">
import { Bold, Eraser, Italic, List, ListOrdered } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';

const props = withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
    }>(),
    {
        placeholder: 'Escribe una descripcion con formato basico.',
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const editor = ref<HTMLDivElement | null>(null);

const syncEditorFromModel = (value: string) => {
    if (!editor.value || editor.value.innerHTML === value) {
        return;
    }

    editor.value.innerHTML = value;
};

const syncModelFromEditor = () => {
    emit('update:modelValue', editor.value?.innerHTML ?? '');
};

const applyCommand = (command: string, value?: string) => {
    editor.value?.focus();
    document.execCommand(command, false, value);
    syncModelFromEditor();
};

onMounted(() => {
    syncEditorFromModel(props.modelValue);
});

watch(
    () => props.modelValue,
    (value) => {
        syncEditorFromModel(value);
    },
);
</script>

<template>
    <div
        class="overflow-hidden rounded-2xl border border-border/60 bg-background"
    >
        <div
            class="flex flex-wrap items-center gap-2 border-b border-border/60 bg-muted/30 p-3"
        >
            <Button
                type="button"
                variant="outline"
                size="sm"
                class="cursor-pointer"
                @click="applyCommand('bold')"
            >
                <Bold class="size-4" />
            </Button>

            <Button
                type="button"
                variant="outline"
                size="sm"
                class="cursor-pointer"
                @click="applyCommand('italic')"
            >
                <Italic class="size-4" />
            </Button>

            <Button
                type="button"
                variant="outline"
                size="sm"
                class="cursor-pointer"
                @click="applyCommand('insertUnorderedList')"
            >
                <List class="size-4" />
            </Button>

            <Button
                type="button"
                variant="outline"
                size="sm"
                class="cursor-pointer"
                @click="applyCommand('insertOrderedList')"
            >
                <ListOrdered class="size-4" />
            </Button>

            <Button
                type="button"
                variant="outline"
                size="sm"
                class="cursor-pointer"
                @click="applyCommand('removeFormat')"
            >
                <Eraser class="size-4" />
            </Button>
        </div>

        <div
            ref="editor"
            contenteditable="true"
            :data-placeholder="placeholder"
            class="personal-rich-editor min-h-44 px-4 py-3 text-sm leading-6 text-foreground outline-none"
            @input="syncModelFromEditor"
        />
    </div>
</template>
