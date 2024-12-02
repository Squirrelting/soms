<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";


const props = defineProps({
    errors: Object,
    signatory: Object,
});

const form = useForm({
    name: props.signatory.name,
    position: props.signatory.position,
});

// Function to update signatory with confirmation
const updateSignatory = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to edit this signatory data!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Updating...",
                text: "Please wait while we update the new signatory.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            form.put(route("signatory.update", props.signatory.id), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Updated!",
                        text: "The signatory has been updated successfully.",
                        confirmButtonText: "OK",
                    });
                    form.reset(); // Reset the form after successful submission
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to update the signatory. Please try again.",
                        confirmButtonText: "OK",
                    });
                    console.error("Failed to Update signatory");
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Edit Signatory" />

    <AuthenticatedLayout>
        <div v-if="$page.props.flash.message" class="alert">
            {{ $page.props.flash.message }}
        </div>

        <div class="mt-4 mx-4">
            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

            <div class="flex justify-between">
                <h5 class="m-4">Edit Signatory</h5>
                <Link
                    :href="route('signatory.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link
                >
            </div>

            <form @submit.prevent="updateSignatory()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>Signatory Name</label>
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
                            <label>Position</label>
                            <input
                                type="text"
                                v-model="form.position"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.position" class="text-red-500">
                                {{ errors.position }}
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
