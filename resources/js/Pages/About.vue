<template lang="">
    <AuthenticatedLayout>
        <Head title=" About Page" />
        <h1> This is {{title}} </h1>
    </AuthenticatedLayout>
</template>

<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';  
        defineProps({
        title: String
    });
</script>

<style lang="scss" scoped>

</style>