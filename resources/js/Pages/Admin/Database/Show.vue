<template>
    <AdminLayout>
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">Table: {{ table }}</h1>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
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
            <div class="mt-4">
                <Link :href="route('admin.database.index')" class="text-blue-500 hover:underline">Back to Tables</Link>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps(['table', 'columns']);
</script>   