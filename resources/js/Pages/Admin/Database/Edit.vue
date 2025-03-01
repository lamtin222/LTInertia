<template>
    <AdminLayout>
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">Edit Table: {{ table }}</h1>
            <form @submit.prevent="form.put(route('admin.database.update', table))">
                <div class="mb-4">
                    <label class="block text-gray-700">Table Name</label>
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
                    <div class="border border-gray-300 rounded overflow-hidden">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 text-left">Name</th>
                                    <th class="py-2 px-4 text-left">Type</th>
                                    <th class="py-2 px-4 text-left">Length</th>
                                    <th class="py-2 px-4 text-left">Not NULL</th>
                                    <th class="py-2 px-4 text-left">Unsigned</th>
                                    <th class="py-2 px-4 text-left">Auto Increment</th>
                                    <th class="py-2 px-4 text-left">Default</th>
                                    <th class="py-2 px-4 text-left">Comment</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(column, index) in form.columns" :key="index" class="border-b">
                                    <td class="py-2 px-4">
                                        <input
                                            v-model="column.name"
                                            type="text"
                                            class="w-full p-1 border rounded"
                                            required
                                        />
                                    </td>
                                    <td class="py-2 px-4">
                                        <select v-model="column.type" class="w-full p-1 border rounded text-sm" required>
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
                                            </optgroup>
                                        </select>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input
                                            v-model="column.length"
                                            type="number"
                                            class="w-full p-1 border rounded"
                                            :disabled="['text', 'json'].includes(column.type)"
                                        />
                                    </td>
                                    <td class="py-2 px-4">
                                        <input v-model="column.required" type="checkbox" class="h-4 w-4" />
                                    </td>
                                    <td class="py-2 px-4">
                                        <input
                                            v-model="column.unsigned"
                                            type="checkbox"
                                            class="h-4 w-4"
                                            :disabled="!['integer', 'bigInteger', 'smallInteger', 'tinyInteger'].includes(column.type)"
                                        />
                                    </td>
                                    <td class="py-2 px-4">
                                        <input
                                            v-model="column.auto_increment"
                                            type="checkbox"
                                            class="h-4 w-4"
                                            :disabled="!['integer', 'bigInteger', 'smallInteger', 'tinyInteger'].includes(column.type)"
                                        />
                                    </td>
                                    <td class="py-2 px-4">
                                        <input
                                            v-model="column.default"
                                            type="text"
                                            class="w-full p-1 border rounded"
                                        />
                                    </td>
                                    <td class="py-2 px-4">
                                        <input
                                            v-model="column.comment"
                                            type="text"
                                            class="w-full p-1 border rounded"
                                        />
                                    </td>
                                    <td class="py-2 px-4">
                                        <button
                                            type="button"
                                            @click="removeColumn(index)"
                                            class="text-red-500 hover:text-red-700"
                                            title="Remove Column"
                                        >
                                            <MinusCircleIcon class="h-6 w-6" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="form.errors.columns" class="text-red-500 text-sm mt-1">
                        {{ form.errors.columns }}
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <Link :href="route('admin.database.index')" class="text-gray-500 hover:underline">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 disabled:bg-gray-400"
                    >
                        Update Table
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { PlusCircleIcon, MinusCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps(['table', 'columns']);



const mapTypeToLaravel = (type) => {
    const typeMap = {
        'varchar': 'string',
        'text': 'text',
        'char': 'char',
        'int': 'integer',
        'bigint': 'bigInteger',
        'smallint': 'smallInteger',
        'tinyint': 'tinyInteger',
        'float': 'float',
        'double': 'double',
        'decimal': 'decimal',
        'date': 'date',
        'datetime': 'dateTime',
        'time': 'time',
        'timestamp': 'timestamp',
        'year': 'year',
        'tinyint(1)': 'boolean',
        'json': 'json',
    };
    return Object.keys(typeMap).reduce((acc, key) => type.includes(key) ? typeMap[key] : acc, 'string');
};

const extractLength = (type) => {
    const match = type.match(/\((\d+)\)/);
    return match ? match[1] : '';
};

const addColumn = () => {
    form.columns.push({
        name: '',
        type: 'string',
        length: '',
        required: false,
        unsigned: false,
        auto_increment: false,
        default: '',
        comment: '',
        old_name: '',
        action: 'modify',
    });
};

const removeColumn = (index) => {
    if (form.columns[index].old_name) {
        form.columns[index].action = 'drop';
    } else {
        form.columns.splice(index, 1);
    }
};
const form = useForm({
    name: props.table,
    columns: props.columns.map(col => ({
        name: col.Field,
        type: mapTypeToLaravel(col.Type),
        length: extractLength(col.Type),
        required: col.Null === 'NO',
        unsigned: col.Type.includes('unsigned'),
        auto_increment: col.Extra.includes('auto_increment'), // Kiểm tra auto_increment
        default: col.Default || '',
        comment: col.Extra.includes('COMMENT') ? col.Extra.split('COMMENT ')[1] : '',
        old_name: col.Field,
        action: 'modify',
    })),
});
</script>