<template>
    <Head title="Admin | {{ dataType.name }}" />
    <AdminLayout>
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">{{ dataType.name }}</h1>
            <div class="mb-4">
                <Link :href="route('admin.bread.create', dataType.slug)" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                    Add New
                </Link>
            </div>
            <div v-if="$page.props.flash && $page.props.flash.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ $page.props.flash.message }}
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th v-for="row in dataType.rows.filter(r => r.browse)" :key="row.id" class="py-3 px-6 text-left">
                                {{ row.display_name }}
                            </th>
                            <th class="py-3 px-6 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b">
                            <td v-for="row in dataType.rows.filter(r => r.browse)" :key="row.id" class="py-3 px-6">
                                {{ item[row.field] }}
                            </td>
                            <td class="py-3 px-6 flex space-x-2">
                                <Link :href="route('admin.bread.show', [dataType.slug, item.id])" class="text-blue-500 hover:underline">View</Link>
                                <Link :href="route('admin.bread.edit', [dataType.slug, item.id])" class="text-yellow-500 hover:underline">Edit</Link>
                                <button @click="deleteItem(item.id)" class="text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import { Link, usePage,Head } from '@inertiajs/vue3';
// import { Inertia } from '@inertiajs/inertia';

defineProps(['dataType', 'items']);

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        Inertia.delete(route('admin.bread.destroy', [usePage().props.dataType.slug, id]));
    }
}
</script>