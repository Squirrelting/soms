<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    errors: Object,
});

const form = useForm({
    minor_offenses: "", 
});

const saveMinorOffense = () => {
    if (form.minor_offenses === '') {
        form.post(route('minoroffense.store'));
    } else {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to add this minor offense!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, save it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Saving...",
                text: "Please wait while we save this minor offense.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            form.post(route("minoroffense.store"), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Minor Offense Added",
                        text: "The minor offense has been successfully added!",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    form.reset();
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem saving the minor offense. Please try again.",
                    });
                },
            });
        }
    });
}
};
</script>




<template>
    <Head title="Add Minor Offense" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

            <div class="flex justify-between">
                <h5 class="m-4">Add Minor Offense</h5>
                <Link
                    :href="route('minoroffense.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link>
            </div>

            <form @submit.prevent="saveMinorOffense()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">

                        <div class="mb-3">
                            <label>Minor Offense</label>
                            <input
                                type="text"
                                v-model="form.minor_offenses"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.minor_offenses" class="text-red-500">
                                {{ errors.minor_offenses }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                            >
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </AuthenticatedLayout>
</template>
