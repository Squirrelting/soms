<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    User: {
        type: Array,
    },
});

const form = useForm({});

const DeleteUser = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Deleting...",
                text: "Please wait while we delete the registered user data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            router.delete(route("user.destroy", id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "The user has been deleted.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire(
                        "Error!",
                        "Failed to delete the registered user.",
                        "error"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Registered User" />

    <AuthenticatedLayout>
        <!-- Message from RegisteredUserController -->
        <div v-if="$page.props.flash.message" class="alert alert-info mt-4 mx-5 px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $page.props.flash.message }}</span>
        </div>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registered User Page</h2>
        </template>

        <!-- User List Table -->
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Registered User List</h5>
                <Link :href="route('user.add')" class="bg-blue-500 text-white p-3 rounded mb-4">Register User</Link>
            </div>
            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">Registered User Email</th>
                        <th class="py-2 px-4 text-left border">Name</th>
                        <th class="py-2 px-4 text-left border">Roles</th>
                        <th class="py-2 px-4 text-left border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in User" :key="index">
                        <td class="py-2 px-4 border">{{ user.email }}</td>
                        <td class="py-2 px-4 border">{{ user.name }}</td>
                        <td class="py-2 px-4 border">{{ user.roles[0].name }}</td>
                        <td>
                            <div v-if="!['admin', 'super-admin'].includes(user.roles[0].name)">
                                <Link :href="route('user.edit', user.id)" class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded me-2 inline-block">
                                Edit
                            </Link>
                            <button @click="DeleteUser(user.id)" class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded me-2 inline-block">
                                Delete
                            </button>
                        </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
