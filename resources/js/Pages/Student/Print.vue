<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 
</script>

<template lang="">
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
        <div class="flex justify-between">
            <h5 class="m-4">Print Certificate of  Good Moral</h5>
            <Link :href="route('dashboard')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
        </div>
    </div>
    </AuthenticatedLayout>
</template>


