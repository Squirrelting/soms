<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    errors: Object,
    user: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "", // Start with an empty password field
});

const updateUser = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this user's information?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Updating...",
                text: "Please wait while we update the user data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Prepare form data
            let data = { name: form.name, email: form.email };
            if (form.password) {
                data.password = form.password; // Only include password if it's been changed
            }

            form.put(route("user.update", props.user.id), {
                data: data, // Pass only necessary data
                onSuccess: () => {
                    Swal.fire(
                        "Updated!",
                        "User information has been updated.",
                        "success"
                    );
                    form.reset(); // Reset form on success
                },
                onError: () =>
                    Swal.fire("Error", "Failed to update the user.", "error"),
            });
        }
    });
};
</script>
    
<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">
                <div class="flex justify-between">
                    <h5 class="m-4">Edit User</h5>
                    <Link
                        :href="route('user.index')"
                        class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >
                        Back
                    </Link>
                </div>

                <form @submit.prevent="updateUser()">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <div class="mb-3">
                                <label>User Name</label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                                />
                                <div v-if="errors.name" class="text-red-500">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>User Email</label>
                                <input
                                    type="text"
                                    v-model="form.email"
                                    class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                                />
                                <div v-if="errors.email" class="text-red-500">
                                    {{ errors.email }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>User Password</label>
                                <input
                                    type="password"
                                    v-model="form.password"
                                    class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                                />
                                <div v-if="errors.password" class="text-red-500">
                                    {{ errors.password }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                                >
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
