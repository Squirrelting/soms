<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({
    errors: Object,
});

const form = useForm({
    name: "",
    email: "",
    role: "",
    password: "",
    password_confirmation: "",
});

const submitForm = () => {
    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to register this user!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, register!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Registering...",
                text: "Please wait while we register the user.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            // Proceed with form submission if confirmed
            form.post(route("user.store"), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "User Registered",
                        text: "The user has been successfully registered!",
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    form.reset("password", "password_confirmation");
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem registering the user. Please try again.",
                    });
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Register User" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Register User</h5>
                <Link
                    :href="route('user.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link>
            </div>

            <form @submit.prevent="submitForm()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input
                                id="name"
                                type="text"
                                v-model="form.name"
                                class="py-1 w-full"
                                required
                            />
                            <div v-if="errors.name" class="text-red-500">
                                {{ errors.name }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                class="py-1 w-full"
                                required
                            />
                            <div v-if="errors.email" class="text-red-500">
                                {{ errors.email }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Role</label>
                            <select v-model="form.role" class="py-1 w-full">
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="pod">POD</option>
                            </select>
                            <div v-if="errors.role" class="text-red-500">
                                {{ errors.role }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password">Password</label>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                class="py-1 w-full"
                                required
                            />
                            <div v-if="errors.password" class="text-red-500">
                                {{ errors.password }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                class="py-1 w-full"
                                required
                            />
                            <div v-if="errors.password_confirmation" class="text-red-500">
                                {{ errors.password_confirmation }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                            >
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Register</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
