<template>
    <AdminLayout>
        <Head :title="`Admin | Create New ${dataType?.name || 'Item'}`" />
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">Create {{ dataType?.name || 'Item' }}</h1>
            <form @submit.prevent="form.post(route('admin.bread.store', dataType?.slug))">
                <div v-if="dataType && dataType.rows && dataType.rows.length > 0">
                    <div v-for="row in dataType.rows.filter(r => r.add)" :key="row.id" class="mb-4">
                        <label class="block text-gray-700">{{ row.display_name }}</label>
                        <input
                            v-if="row.type === 'text' || row.type === 'number' || row.type === 'date' || row.type === 'datetime-local' || row.type === 'time'"
                            v-model="form[row.field]"
                            :type="row.type"
                            :required="row.required"
                            class="w-full p-2 border rounded"
                            :class="{ 'border-red-500': form.errors[row.field] }"
                        />
                        <textarea
                            v-else-if="row.type === 'textarea'"
                            v-model="form[row.field]"
                            class="w-full p-2 border rounded"
                            rows="5"
                            :required="row.required"
                            :class="{ 'border-red-500': form.errors[row.field] }"
                        ></textarea>
                        <TinyMCE
                            v-else-if="row.type === 'tinymce'"
                            v-model="form[row.field]"
                            :id="`tinymce-create-${row.id}`"
                            :required="row.required"
                            :class="{ 'border-red-500': form.errors[row.field] }"
                        />
                        <input
                            v-else-if="row.type === 'checkbox'"
                            v-model="form[row.field]"
                            type="checkbox"
                            class="h-4 w-4"
                            :required="row.required"
                        />
                        <div v-if="form.errors[row.field]" class="text-red-500 text-sm mt-1">
                            {{ form.errors[row.field] }}
                        </div>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 disabled:bg-gray-400"
                    >
                        Save
                    </button>
                </div>
                <div v-else class="text-red-500">
                    No editable fields available for this table. Please configure BREAD first.
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import TinyMCE from '../../../Components/TinyMCE.vue';
import { useForm, Head } from '@inertiajs/vue3';

const props = defineProps(['dataType']);

const form = useForm(
    props.dataType && props.dataType.rows
        ? Object.fromEntries(props.dataType.rows.filter(r => r.add).map(row => [row.field, '']))
        : {}
);
</script>