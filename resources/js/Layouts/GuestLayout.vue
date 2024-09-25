<script setup>
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
                <img class="w-20 h-20 rounded-full" src="/Images/SCNHS-Logo.png" alt="logo here">
            </Link>
        </div>
            <slot />
        </div>
    </div>
</template>
