<template>
    <AdminLayout>
        <Head :title="`Admin | Create New Table`" />
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">Create New Table</h1>
            <form @submit.prevent="form.post(route('admin.database.store'))">
                <div class="mb-4">
                    <label class="block text-gray-700">Table Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full p-1 border rounded"
                        required
                        placeholder="e.g., categories"
                        :class="{ 'border-red-500': form.errors.name }"
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                        {{ form.errors.name }}
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center mb-2">
                        <h2 class="text-xl font-semibold">Columns</h2>
                        <button
                            type="button"
                            @click="addColumn"
                            class="text-green-500 hover:text-green-700 ml-2"
                            title="Add Column"
                        >
                            <PlusCircleIcon class="h-6 w-6" />
                        </button>
                    </div>
                    <div class="border border-gray-300 rounded p-4">
                        <div v-for="(column, index) in form.columns" :key="index" class="flex space-x-2 mb-2 items-center">
                            <input
                                v-model="column.name"
                                type="text"
                                class="w-1/5 p-1 border rounded"
                                placeholder="Name"
                                required
                            />
                            <select v-model="column.type" class="w-1/5 p-1 border rounded" required>
                                <optgroup label="Basic Types">
                                    <option value="string">String (VARCHAR)</option>
                                    <option value="text">Text (TEXT)</option>
                                    <option value="char">Char (CHAR)</option>
                                </optgroup>
                                <optgroup label="Numeric Types">
                                    <option value="integer">Integer (INT)</option>
                                    <option value="bigInteger">Big Integer (BIGINT)</option>
                                    <option value="smallInteger">Small Integer (SMALLINT)</option>
                                    <option value="tinyInteger">Tiny Integer (TINYINT)</option>
                                    <option value="float">Float (FLOAT)</option>
                                    <option value="double">Double (DOUBLE)</option>
                                    <option value="decimal">Decimal (DECIMAL)</option>
                                </optgroup>
                                <optgroup label="Date/Time Types">
                                    <option value="date">Date (DATE)</option>
                                    <option value="dateTime">DateTime (DATETIME)</option>
                                    <option value="time">Time (TIME)</option>
                                    <option value="timestamp">Timestamp (TIMESTAMP)</option>
                                    <option value="year">Year (YEAR)</option>
                                </optgroup>
                                <optgroup label="Special Types">
                                    <option value="boolean">Boolean (TINYINT(1))</option>
                                    <option value="json">JSON (JSON)</option>
                                    <option value="timestamps">Timestamps</option>
                                </optgroup>
                            </select>
                            <input
                                v-model="column.length"
                                type="number"
                                class="w-1/6 p-1 border rounded"
                                placeholder="Length"
                                :disabled="['text', 'timestamps', 'json'].includes(column.type)"
                            />
                            <label class="flex items-center w-1/6">
                                <input v-model="column.required" type="checkbox" class="mr-1" />
                                Required
                            </label>
                            <input
                                v-model="column.default"
                                type="text"
                                class="w-1/5 p-1 border rounded"
                                placeholder="Default"
                            />
                            <input
                                v-model="column.comment"
                                type="text"
                                class="w-1/5 p-1 border rounded"
                                placeholder="Comment"
                            />
                            <button
                                type="button"
                                @click="removeColumn(index)"
                                class="text-red-500 hover:text-red-700"
                                title="Remove Column"
                            >
                                <MinusCircleIcon class="h-6 w-6" />
                            </button>
                        </div>
                    </div>
                    <div v-if="form.errors.columns" class="text-red-500 text-sm mt-1">
                        {{ form.errors.columns }}
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 disabled:bg-gray-400"
                    >
                        Create Table
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { PlusCircleIcon, MinusCircleIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    columns: [],
});

const addColumn = () => {
    form.columns.push({
        name: '',
        type: 'string',
        length: '',
        required: false,
        default: '',
        comment: '',
    });
};

const removeColumn = (index) => {
    form.columns.splice(index, 1);
};
</script>