<template>
    <Head :title="`Admin | Configure BREAD for ${dataType.name}`" />
    <AdminLayout>
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">Configure BREAD for {{ dataType.name }}</h1>
            <form @submit.prevent="saveConfiguration">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700">Display Name Singular</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full p-1 border rounded"
                            required
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700">Slug</label>
                        <input
                            v-model="form.slug"
                            type="text"
                            class="w-full p-1 border rounded"
                            required
                            :class="{ 'border-red-500': form.errors.slug }"
                        />
                        <div v-if="form.errors.slug" class="text-red-500 text-sm mt-1">
                            {{ form.errors.slug }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700">Icon (e.g., fas fa-table)</label>
                        <input
                            v-model="form.icon"
                            type="text"
                            class="w-full p-1 border rounded"
                            placeholder="Font Awesome class"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700">Model Namespace</label>
                        <input
                            v-model="form.model_name"
                            type="text"
                            class="w-full p-1 border rounded"
                            required
                            :class="{ 'border-red-500': form.errors.model_name }"
                        />
                        <div v-if="form.errors.model_name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.model_name }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700">Controller Namespace (Optional)</label>
                        <input
                            v-model="form.controller_name"
                            type="text"
                            class="w-full p-1 border rounded"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700">Policy Name (Optional)</label>
                        <input
                            v-model="form.policy_name"
                            type="text"
                            class="w-full p-1 border rounded"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700">Scope (Optional)</label>
                        <input
                            v-model="form.scope"
                            type="text"
                            class="w-full p-1 border rounded"
                            placeholder="e.g., active"
                        />
                        <small class="text-gray-500">Enter a scope method name (e.g., 'active' for scopeActive)</small>
                    </div>
                    <div>
                        <label class="block text-gray-700">Order Column (Optional)</label>
                        <input
                            v-model="form.order_column"
                            type="text"
                            class="w-full p-1 border rounded"
                            placeholder="e.g., id"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700">Order Direction</label>
                        <select v-model="form.order_direction" class="w-full p-1 border rounded">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input
                            v-model="form.generate_permissions"
                            type="checkbox"
                            class="h-4 w-4 mr-2"
                        />
                        <label class="text-gray-700">Generate Permissions</label>
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="text-xl font-semibold mb-2">Fields Configuration</h2>
                    <div v-if="form.rows && form.rows.length > 0" class="border border-gray-300 rounded overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 text-left"></th>
                                    <th class="py-2 px-4 text-left">REQ</th>
                                    <th class="py-2 px-4 text-left w-1/10">Field</th>
                                    <th class="py-2 px-4 text-left w-1/4">Display Name</th>
                                    <th class="py-2 px-4 text-left w-1/4">Type</th>                                    
                                    <th class="py-2 px-4 text-left w-1/6">Options</th>
                                    <th class="py-2 px-4 text-left">Details (JSON)</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <draggable v-model="form.rows" item-key="id" tag="tbody" handle=".handle">
                                <template #item="{ element: row, index }">
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-2 px-4">
                                            <button type="button" class="handle text-gray-500 cursor-move" title="Drag to reorder">
                                                <Bars3Icon class="h-6 w-6" />
                                            </button>
                                        </td>
                                        <!-- required -->
                                        <td class="py-2 px-4">
                                            <input v-model="row.required" type="checkbox" class="h-4 w-4" />
                                        </td>
                                        <!-- field -->
                                        <td class="py-2 px-4">
                                            <label class="w-full p-1 border rounded">
                                                {{ row.field }}
                                            </label>
                                        </td>
                                        <!-- display name -->
                                        <td class="py-2 px-4">
                                            <input v-model="row.display_name" class="w-full p-1 border rounded" required />
                                        </td>
                                        <!-- type -->
                                        <td class="py-2 px-4">
                                            <select v-model="row.type" class="w-full p-1 border rounded" required @change="updateDefaultDetails(row)">
                                                <option value="text">Text</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="number">Number</option>
                                                <option value="checkbox">Checkbox</option>
                                                <option value="date">Date</option>
                                                <option value="datetime-local">DateTime</option>
                                                <option value="time">Time</option>
                                                <option value="select">Select</option>
                                                <option value="tinymce">TinyMCE</option>
                                            </select>
                                        </td>
                                        
                                        <!-- add permission -->
                                        <td class="py-2 px-4">
                                            <div class="flex flex-col space-y-1">
                                                <label class="flex items-center">
                                                    <input v-model="row.browse" type="checkbox" class="h-4 w-4 mr-1" />
                                                    Browse
                                                </label>
                                                <label class="flex items-center">
                                                    <input v-model="row.read" type="checkbox" class="h-4 w-4 mr-1" />
                                                    Read
                                                </label>
                                                <label class="flex items-center">
                                                    <input v-model="row.edit" type="checkbox" class="h-4 w-4 mr-1" />
                                                    Edit
                                                </label>
                                                <label class="flex items-center">
                                                    <input v-model="row.add" type="checkbox" class="h-4 w-4 mr-1" />
                                                    Add
                                                </label>
                                                <label class="flex items-center">
                                                    <input v-model="row.delete" type="checkbox" class="h-4 w-4 mr-1" />
                                                    Delete
                                                </label>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4">
                                            <Vue3JsonEditor
                                                v-model="row.details"
                                                :expanded-on-start="true"
                                                :modes="['tree', 'code']"
                                                :mode="'code'"
                                                :default-value="{}"
                                                style="height: 300px;"
                                            />
                                        </td>
                                        <td class="py-2 px-4">
                                            <button
                                                type="button"
                                                @click="removeRow(index)"
                                                class="text-red-500 hover:text-red-700"
                                                title="Remove Row"
                                            >
                                                <MinusCircleIcon class="h-6 w-6" />
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </draggable>
                        </table>
                    </div>
                    <div v-else class="text-red-500 mb-4">
                        No fields configured yet.
                    </div>
                    <button
                        type="button"
                        @click="addRow"
                        class="mt-2 text-green-500 hover:text-green-700"
                    >
                        <PlusCircleIcon class="h-6 w-6" />
                    </button>
                </div>

                <div class="flex justify-end space-x-2">
                    <Link :href="route('admin.bread.index', dataType.slug)" class="text-gray-500 hover:underline">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 disabled:bg-gray-400"
                    >
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { PlusCircleIcon, MinusCircleIcon, Bars3Icon } from '@heroicons/vue/24/outline';
import Draggable from 'vuedraggable';
import {Vue3JsonEditor} from 'vue3-json-editor';

const props = defineProps(['dataType']);

const form = useForm({
    name: props.dataType.name,
    slug: props.dataType.slug,
    icon: props.dataType.icon || '',
    model_name: props.dataType.model_name,
    controller_name: props.dataType.controller_name || '',
    policy_name: props.dataType.policy_name || '',
    generate_permissions: props.dataType.generate_permissions || false,
    scope: props.dataType.scope || '',
    order_column: props.dataType.order_column || '',
    order_direction: props.dataType.order_direction || 'asc',
    rows: props.dataType.rows.map(row => ({
        id: row.id,
        field: row.field,
        type: row.type,
        display_name: row.display_name,
        required: row.required,
        browse: row.browse,
        read: row.read,
        edit: row.edit,
        add: row.add,
        delete: row.delete,
        order: row.order,
        details: row.details ? JSON.parse(row.details || '{}') : {}, // Đảm bảo giá trị mặc định là {}
    })),
});

const addRow = () => {
    form.rows.push({
        id: null,
        field: '',
        type: 'text',
        display_name: '',
        required: false,
        browse: true,
        read: true,
        edit: true,
        add: true,
        delete: true,
        order: form.rows.length + 1,
        details: {}, // Giá trị mặc định là object rỗng
    });
};

const removeRow = (index) => {
    form.rows.splice(index, 1);
};

const saveConfiguration = () => {
    form.rows.forEach((row, index) => {
        row.order = index + 1; // Gán lại order theo thứ tự kéo thả
    });
    form.put(route('admin.bread.configure.save', props.dataType.slug));
};

const updateDefaultDetails = (row) => {
    if (row.type === 'select' && !Object.keys(row.details).length) {
        row.details = { options: ['option1', 'option2'] }; // Giá trị mặc định cho select
    }
};
</script>

<style >
.jsoneditor-vue .jsoneditor-menu {  /* Thay đổi selector nếu cần */
    display: none;  /* Ẩn tiêu đề */
}
</style>