<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import Swal from 'sweetalert2'; // Import SweetAlert2

// Define props with correct types
const props = defineProps({
    errors: Object,
    signatory: Array, // Update to Array since you are looping through it
    student: Object,
});

// Use form with Inertia's useForm hook
const form = useForm({
    signatory: '',
});

// Function to handle print logic
const handlePrint = () => {
    if (!form.signatory) {
        // Show SweetAlert warning when no signatory is selected
        Swal.fire({
            icon: 'error',
            title: 'No Signatory Selected',
            text: 'Please select a signatory before printing the certificate.',
        });
    }
};
</script>

<template>
    <Head title="Print" />
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Print Certificate of Good Moral</h5>
                <Link :href="route('students.index')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
            </div>
        </div>

        <form @submit.prevent="handlePrint"> <!-- Prevent default form submission -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        <label>Select Signatory</label>
                        <select v-model="form.signatory" class="py-1 w-full">
                            <option value="">Select Signatory</option>
                            <option v-for="sign in signatory" :key="sign.id" :value="sign.id">
                                {{ sign.name }}, {{ sign.position }}
                            </option>
                        </select>
                        <div v-if="errors.signatory" class="text-red-500">{{ errors.signatory }}</div>
                    </div>
                    <div class="mb-3">
                        <a
                            target="_blank"
                            v-if="form.signatory" 
                            :href="`/students/print-certificate/${form.signatory}/${props.student.id}`" 
                            class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                        >
                            Print
                        </a>
                        <button 
                            v-else 
                            type="button" 
                            @click="handlePrint" 
                            class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                        >
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
