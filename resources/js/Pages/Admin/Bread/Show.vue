<template>
    <AdminLayout>
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-6">{{ dataType.name }} Details</h1>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <dl v-for="row in dataType.rows.filter(r => r.read)" :key="row.id" class="mb-4">
                    <dt class="font-semibold">{{ row.display_name }}</dt>
                    <dd class="text-gray-600">{{ item[row.field] }}</dd>
                </dl>
                <div class="mt-4">
                    <Link :href="route('admin.bread.edit', [dataType.slug, item.id])" class="text-yellow-500 hover:underline mr-4">Edit</Link>
                    <Link :href="route('admin.bread.index', dataType.slug)" class="text-blue-500 hover:underline">Back</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../../Components/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps(['dataType', 'item']);
</script>