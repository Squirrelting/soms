<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import Swal from 'sweetalert2'; 

const props = defineProps({
    errors: Object,
    signatory: Array, 
});

const form = useForm({
    firstname: "",
    middlename: "",
    lastname: "",
    signatory: '',
});

const handlePrint = () => {
    if (!form.firstname || !form.lastname || !form.signatory) {
        Swal.fire({
            icon: 'error',
            title: 'Incomplete Information',
            text: 'Please fill in the student\'s first name, last name, and select a signatory before printing the certificate.',
        });
    }
};

const formatName = (name) => {
    return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
};

</script>

<template>
    <Head title="Print" />
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">

            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

        <form @submit.prevent="handlePrint">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="grid grid-cols-12 gap-4">
            <div class="col-span-4 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Student's First Name</label>
                <input type="text" v-model="form.firstname" @input="form.firstname = formatName(form.firstname)" 
                class="py-1 w-full bg-gray-200 border border-gray-500 rounded" />
                <div v-if="errors.firstname" class="text-red-500">{{ errors.firstname }}</div>
            </div>
            <div class="col-span-4 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Student's Middle Name (Optional)</label>
                <input type="text" v-model="form.middlename" @input="form.middlename = formatName(form.middlename)" 
                class="py-1 w-full bg-gray-200 border border-gray-500 rounded" />
                <div v-if="errors.middlename" class="text-red-500">{{ errors.middlename }}</div>
            </div>
            <div class="col-span-4 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Student's Last Name</label>
                <input type="text" v-model="form.lastname" @input="form.lastname = formatName(form.lastname)" 
                class="py-1 w-full bg-gray-200 border border-gray-500 rounded" />
                <div v-if="errors.lastname" class="text-red-500">{{ errors.lastname }}</div>
            </div>
        </div>
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
                            :href="`/print/print-certificate/${form.signatory}/${encodeURIComponent(form.firstname)}/${encodeURIComponent(form.middlename)}/${encodeURIComponent(form.lastname)}`"
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
    </div>
    </div>
    </AuthenticatedLayout>
</template>