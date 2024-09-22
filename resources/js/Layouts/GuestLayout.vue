<script setup>
import Logo from '@/Pages/Images/SCNHS-Logo.png';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="bg-black min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo at the top -->
        <!-- Form container -->
        <div
            class="z-10 bg-white w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg"
        >
        <div class="mb-6 flex flex-center items-center w-full flex-col">
            <Link href="/">
                <img :src="Logo" alt="SCNHS Logo" class="w-20 h-20" />
            </Link>
        </div>
            <slot />
        </div>
    </div>
</template>
