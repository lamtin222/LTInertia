<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-4">
                <h1 class="text-2xl font-bold">Voyager</h1>
            </div>
            <nav class="mt-4">
                <a href="/admin" class="block py-2 px-4 hover:bg-gray-700">Dashboard</a>
                <a href="/admin/database" class="block py-2 px-4 hover:bg-gray-700">Database</a>
               
                <a v-for="dataType in dataTypes" :key="dataType.id" :href="`/admin/${dataType.slug}`" 
                                        class="block py-2 px-4 hover:bg-gray-700">
                {{ dataType.name }}
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Admin Panel</h2>
                <div>
                    <span class="mr-4">{{ user.name }}</span>
                    <a href="/logout" class="text-blue-500 hover:underline">Logout</a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
const user = usePage().props.auth.user;
const dataTypes = usePage().props.dataTypes || [];
</script>