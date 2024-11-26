<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import axios from 'axios'; // Import axios

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = async () => {
    try {
        // Check account status with Axios
        const response = await axios.post("/get-status", {
            email: form.email,
            password: form.password, // Include password for validation
        });

        // If account is deactivated, show Swal error
        if (response.data.error) {
            Swal.fire({
                icon: 'error',
                title: 'Account Deactivated',
                text: response.data.error,
                showConfirmButton: true
            });
            return; // Stop further execution
        }

        // If account is active, proceed with login
        Swal.fire({
            title: 'Logging in...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        form.post(route('authenticateUser'), {
            onFinish: () => {
                form.reset('password');
            },
            onSuccess: () => {
                // Close the loading indicator and show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Logged in!',
                    text: 'You have been logged in successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            onError: () => {
                // Close the loading indicator if there's an error
                Swal.close();
            }
        });

    } catch (error) {
        // Handle Axios errors (network or unexpected issues)
        console.error("Error during request:", error);

        if (error.response) {
            // Handle validation or server errors
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response.data.error || 'Something went wrong. Please try again later.',
                showConfirmButton: true
            });
        } else {
            // Handle network or unexpected errors
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong. Please try again later.',
                showConfirmButton: true
            });
        }
    }
};
</script>




<template>
            <!-- Background image with dark overlay -->
        <div class="absolute inset-0 z-0">
            <img
                id="background"
                class="w-full h-full object-cover opacity-40"
                src="/Images/SCNHS-Background.jpg"
                alt="Background"
            />
            <div class="absolute z-0"></div>
        </div>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
