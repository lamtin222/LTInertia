<template>
    <div>
        <textarea :id="id" v-model="content" class="w-full"></textarea>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/image';
import 'tinymce/plugins/code';

const props = defineProps({
    modelValue: String,
    id: { type: String, default: 'tinymce-' + Math.random().toString(36).substr(2, 9) },
    required: Boolean,
});

const emit = defineEmits(['update:modelValue']);
const content = ref(props.modelValue);

onMounted(() => {
    tinymce.init({
        selector: `#${props.id}`,
        plugins: 'link lists image code',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
        height: 300,
        skin: false, // Không cần skin vì đã import trực tiếp
        content_css: '/tinymce/skins/ui/oxide/content.min.css',
        base_url: '/tinymce', // Đường dẫn tĩnh đến thư mục public/tinymce
        suffix: '.min',
        setup: (editor) => {
            editor.on('change', () => {
                const newContent = editor.getContent();
                content.value = newContent;
                emit('update:modelValue', newContent);
            });
        },
    });
});

onUnmounted(() => {
    const editor = tinymce.get(props.id);
    if (editor) editor.remove();
});

watch(() => props.modelValue, (newValue) => {
    if (newValue !== content.value) {
        content.value = newValue;
        const editor = tinymce.get(props.id);
        if (editor) editor.setContent(newValue || '');
    }
});
</script>

<style scoped>
.w-full {
    width: 100%;
}
</style>