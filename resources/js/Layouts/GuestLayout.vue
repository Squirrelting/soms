<script setup>
import Logo from '@/Pages/Images/SCNHS-Logo.png';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- Logo at the top -->
        <div class="mb-6">
            <Link href="/">
                <img :src="Logo" alt="SCNHS Logo" class="w-20 h-20" />
            </Link>
        </div>

        <!-- Form container -->
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
