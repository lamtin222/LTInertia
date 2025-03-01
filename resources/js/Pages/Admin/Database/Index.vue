<template>
    <AdminLayout>
        <Head title="Database Manager" />   
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">Database Manager</h1>
            <div class="mb-4">
                <Link :href="route('admin.database.create')" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                    Create New Table
                </Link>
            </div>
            <div v-if="$page.props.flash && $page.props.flash.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ $page.props.flash.message }}
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-6 text-left">Table Name</th>
                            <th class="py-3 px-6 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="table in tables" :key="table" class="border-b">
                            <td class="py-3 px-6">{{ table }}</td>
                            <td class="py-3 px-6 flex space-x-2">
                                <button @click="showTableDetails(table)" class="text-blue-500 hover:text-blue-700 flex items-center" title="View">
                                    <EyeIcon class="h-5 w-5 mr-1" /> View
                                </button>
                                <Link :href="route('admin.database.edit', table)" class="text-yellow-500 hover:text-yellow-700 flex items-center" title="Edit">
                                    <PencilIcon class="h-5 w-5 mr-1" /> Edit
                                </Link>
                                <button @click="deleteTable(table)" class="text-red-500 hover:text-red-700 flex items-center" title="Delete">
                                    <TrashIcon class="h-5 w-5 mr-1" /> Delete
                                </button>
                                <Link :href="route('admin.bread.configure', table)" class="text-blue-500 hover:text-green-700 flex items-center" title="Create BREAD">
                                    
                                    <Cog6ToothIcon class="h-5 w-5 mr-1" /> CONFIG
                                </Link>
                                <Link :href="route('admin.bread.index', table)" class="text-green-500 hover:text-green-700 flex items-center" title="Create BREAD">
                                    <QueueListIcon class="h-5 w-5 mr-1" /> BROWSE
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="selectedTable" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-3/4 max-h-[80vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Table: {{ selectedTable }}</h2>
                        <button @click="selectedTable = null" class="text-red-500 hover:text-red-700">
                            Close
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-3 px-6 text-left">Field</th>
                                    <th class="py-3 px-6 text-left">Type</th>
                                    <th class="py-3 px-6 text-left">Null</th>
                                    <th class="py-3 px-6 text-left">Key</th>
                                    <th class="py-3 px-6 text-left">Default</th>
                                    <th class="py-3 px-6 text-left">Extra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="column in columns" :key="column.Field" class="border-b">
                                    <td class="py-3 px-6">{{ column.Field }}</td>
                                    <td class="py-3 px-6">{{ column.Type }}</td>
                                    <td class="py-3 px-6">{{ column.Null }}</td>
                                    <td class="py-3 px-6">{{ column.Key }}</td>
                                    <td class="py-3 px-6">{{ column.Default }}</td>
                                    <td class="py-3 px-6">{{ column.Extra }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import { Link, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import { EyeIcon, PencilIcon, TrashIcon, DocumentPlusIcon,Cog6ToothIcon,QueueListIcon } from '@heroicons/vue/24/outline';

defineProps(['tables']);

const selectedTable = ref(null);
const columns = ref([]);

const showTableDetails = async (table) => {
    selectedTable.value = table;
    try {
        const response = await fetch(`/admin/database/${table}/columns`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        columns.value = await response.json();
    } catch (error) {
        console.error('Error fetching columns:', error);
        selectedTable.value = null;
    }
};

const deleteTable = (table) => {
    if (confirm(`Are you sure you want to delete the table "${table}"? This action cannot be undone.`)) {
        Inertia.delete(route('admin.database.destroy', table));
    }
};
</script>