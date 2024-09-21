<script setup>
import { Head, Link } from '@inertiajs/vue3';
import backgroundImage from '@/Pages/Images/SCNHS-Background.jpg';
import Logo from '@/Pages/Images/SCNHS-Logo.png';


defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 relative min-h-screen">
        <!-- Background image with dark overlay -->
        <div class="absolute inset-0">
            <img
                id="background"
                class="w-full h-full object-cover opacity-40"
                :src="backgroundImage" 
                alt="Background"
            />
            <div class="absolute inset-0 bg-black opacity-40"></div>
        </div>

        <!-- Main content container -->
        <div class="relative flex flex-col items-center justify-center min-h-screen px-4 py-10 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 py-10 bg-white/90 backdrop-blur-lg shadow-lg rounded-lg lg:max-w-4xl">
        <div class="mb-6">
            <Link href="/">
                <img :src="Logo" alt="SCNHS Logo" class="w-20 h-20" />
            </Link>
        </div>
                <!-- Header with navigation -->
                <header class="flex justify-between items-center gap-4 py-4 mb-8 border-b border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-700 ">Welcome</h1>
                    <nav v-if="canLogin" class="space-x-4">
                        <!-- Dashboard link if user is authenticated -->
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md bg-[#FF2D20] px-4 py-2 text-white font-semibold shadow-md hover:bg-red-600 transition"
                        >
                            Dashboard
                        </Link>

                        <!-- Login and Register links if not authenticated -->
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md bg-gray-800 px-4 py-2 text-white font-semibold shadow-md hover:bg-gray-600 transition"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md bg-gray-600 px-4 py-2 text-white font-semibold shadow-md hover:bg-gray-500 transition"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <!-- Body content (slots or additional content can be added here) -->
                <div class="text-center">
                    <p class="text-gray-600 ">
                        Experience a seamless and efficient way to manage your school communication needs. Log in or register to get started.
                    </p>
                </div>

                <!-- Footer -->
                <footer class="mt-12 text-center text-sm text-gray-500 ">
                    &copy; 2024 SCNHS. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>
